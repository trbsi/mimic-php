<?php

use Illuminate\Database\Seeder;
use App\Api\V2\Mimic\Models\Mimic;
use App\Models\CoreUser;

class MimicsTable extends Seeder
{
    private const THUMB_STRING = '_video_thumb';
    private const DATE = '1970-01-01 12:00:00';
    private const YEAR = '1970';
    private const MONTH = '01';

    /**
     * @var string
     */
    private $rootDir;

    /**
     * Acts like autoincrement so it's always unique
     * @var integer
     */
    private $id;
    private $originalMimicId = 1;
    private $responseMimicId = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Mimic $mimic, CoreUser $user)
    {
        $this->rootDir = public_path() . '/files/seeds';

        $files = $this->generateArrayOfFilesFromSeedFolder();

        //before moving all files to a directore remove old directory
        $this->delDir(public_path() . '/files/user');

        //main mimic and its creator
        $mainUserId = 1;

        //$dirName = Beatbox, HappyDogs, Muscles...
        foreach ($files as $dirName => $filesTmp) {
            $mimicResponses = [];

            //$filesTmp = array of files found in $dirName folder
            foreach ($filesTmp as $arrayKey => $file) {
                //main mimic
                if ($arrayKey === 0) {
                    $userIdTmp = $mainUserId;
                    $this->id = $this->originalMimicId;
                } else { //response mimic
                    $userIdTmp++;
                    $this->id = $this->responseMimicId;
                }

                //get file info
                ['pathParts' => $pathParts, 'mime' => $mime, 'width' => $width, 'height' => $height] = $this->getFileInfo($file, $dirName);

                //copy files to another directory
                ['file_name' => $fileName, 'video_thumb_file_name' => $videoThumbFileName] = $this->copyFilesToDirectory($userIdTmp, $dirName, $file, $pathParts, $mime);

                $data = $this->prepareDataForInsert($pathParts, $fileName, $videoThumbFileName, $dirName, $userIdTmp, $mime, $width, $height);

                //insert into database
                //main mimic
                if ($arrayKey === 0) {
                    $originalMimic = $this->saveOriginalMimic($mimic, $data, $dirName);
                    $this->originalMimicId++;
                } else { //response mimic
                    $mimicResponses[] = $data;
                    $this->responseMimicId++;
                }
            }

            $this->saveResponses($originalMimic, $mimicResponses, $data);
            $mainUserId++;
        }
    }

    /**
     * delete directory recursively
     * @param  string $dir Path to a directory
     * @return bool
     */
    private function delDir($dir)
    {
        if (file_exists($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->delDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
    }


    /**
     * Iterate through /files/seeds folder and create array of files names
     * @return array
     */
    private function generateArrayOfFilesFromSeedFolder(): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->rootDir));

        $files = [];

        foreach ($rii as $path => $file) {
            if ($file->isDir() || $file->getFileName() === 'hashtags.txt') {
                continue;
            }

            //replace all "\" with "/" and take string between "/files/seeds/" and "/"
            //for example: E:\xampp\htdocs\mimic\public/files/seeds/Beatbox/Beatbom.mp4, you take "Beatbox"
            preg_match('/(?<=\/files\/seeds\/)(.*)(?=\/)/', str_replace("\\", "/", $file->getPathname()), $matches);

            //if you don't fine "_video_thumb" in string include it in array
            if (strpos($file->getFileName(), self::THUMB_STRING) === false) {
                $files[$matches[0]][] = $file->getFileName();
            }
        }

        return $files;
    }

    /**
     * @param  string $file
     * @param  string $dirName
     * @return array
     */
    private function getFileInfo(string $file, string $dirName): array
    {
        $absolutePath = sprintf('%s/%s/%s', $this->rootDir, $dirName, $file);
        $pathParts = pathinfo($file);
        $mime = mime_content_type($absolutePath);

        //@TODO this should be video size
        $width = 900;
        $height = 600;

        if (strpos($mime, 'image') !== false) {
            [$width, $height]  = getimagesize($absolutePath);
        }

        return ['pathParts' => $pathParts, 'mime' => $mime, 'width' => $width, 'height' => $height];
    }

    /**
     * @param  int    $userIdTmp
     * @param  string $dirName
     * @param  string $file
     * @param  array  $pathParts
     * @param  string  $mime
     * @return array
     */
    private function copyFilesToDirectory(
        int $userIdTmp,
        string $dirName,
        string $file,
        array $pathParts,
        string $mime
    ): array {
        $path = sprintf('%s/files/user/%s/%s/%s', public_path(), $userIdTmp, self::YEAR, self::MONTH);

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fileName = $this->generateConstantUniqueFileName($userIdTmp, $pathParts['extension']);
        copy(sprintf('%s/%s/%s', $this->rootDir, $dirName, $file), sprintf('%s/%s', $path, $fileName));

        //if this is video file get its thumb, move to another folder and save
        $videoThumbFileName = null;
        if (strpos($mime, 'video') !== false) {
            $videoThumbFileName = $this->generateConstantUniqueFileName($userIdTmp, 'jpg');
            $originalThumbName = $this->getOriginalThumbName($pathParts);
            copy(sprintf('%s/%s/%s', $this->rootDir, $dirName, $originalThumbName), sprintf('%s/%s', $path, $videoThumbFileName));
        }

        return ['file_name' => $fileName, 'video_thumb_file_name' => $videoThumbFileName];
    }

    /**
     * @todo  $width and $height are not used because of expected responses from .json file. If we put different images/videos those values won't be the same and we'll need to adjust tests all the tim
     * 
     * @param  array  $pathParts
     * @param  string $fileName
     * @param  string|null $videoThumbFileName
     * @param  string $dirName
     * @param  int    $userIdTmp
     * @param  string $mime
     * @param  string $width
     * @param  string $height
     * @return array
     */
    private function prepareDataForInsert(
        array $pathParts,
        string $fileName,
        ?string $videoThumbFileName,
        string $dirName,
        int $userIdTmp,
        string $mime,
        string $width,
        string $height
    ): array {
        $data =
        [
            'file' => $fileName,
            'mimic_type' => (strpos($mime, 'image') !== false) ? Mimic::TYPE_PHOTO : Mimic::TYPE_VIDEO,
            'upvote' => 123456789,
            'user_id' => $userIdTmp,
            'created_at' => self::DATE,
            'updated_at' => self::DATE,
            'video_thumb' => $videoThumbFileName,
            'meta' => [
                'height' => 900,
                'width' => 600, 
            ]
        ];

        if ($videoThumbFileName !== null) {
            $originalThumbName = $this->getOriginalThumbName($pathParts);
            ['width' => $width, 'height' => $height] = $this->getFileInfo($originalThumbName, $dirName);

            $data['meta']['thumbnail_height'] = 300;
            $data['meta']['thumbnail_width'] = 200;
        }

        return $data;
    }

    /**
     * @param  Mimic  $mimic
     * @param  array  $data
     * @param  string $dirName
     * @return Mimic
     */
    private function saveOriginalMimic(Mimic $mimic, array $data, string $dirName): Mimic
    {
        //create mimic
        $originalMimic = $mimic->create(array_except($data, ['meta']));
        //insert meta
        $originalMimic->meta()->create(array_get($data, 'meta'));
        //add hashtags for this mimic
        $mimic->saveHashtags(file_get_contents($this->rootDir . '/' . $dirName . '/' . 'hashtags.txt'), $originalMimic);

        return $originalMimic;
    }

    /**
     * @param  Mimic  $originalMimic
     * @param  array  $mimicResponses
     * @param  array  $data
     * @return void
     */
    private function saveResponses(Mimic $originalMimic, array $mimicResponses, array $data): void
    {
        foreach ($mimicResponses as $mimicResponse) {
            $response = $originalMimic->responses()->create(array_except($mimicResponse, ['meta']));
            $response->meta()->create(array_get($data, 'meta'));
        }
    }

    /**
     * @param  array  $pathParts
     * @return string
     */
    private function getOriginalThumbName(array $pathParts): string
    {
        return $pathParts['filename'] . self::THUMB_STRING . ".jpg";
    }

    /**
     * @param  int    $userIdTmp
     * @param  string $extension
     * @return string
     */
    private function generateConstantUniqueFileName(int $userIdTmp, string $extension): string
    {
        //e.g. 2-3.jpg
        $name = sprintf('%s-%s.%s', $userIdTmp, $this->id, $extension);
        return $name;
    }
}
