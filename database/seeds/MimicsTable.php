<?php

use Illuminate\Database\Seeder;
use App\Api\V1\Mimic\Models\Mimic;
use App\Models\CoreUser;

class MimicsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Mimic $mimic, CoreUser $user)
    {
        $users = $user->count();

        $rootDir = public_path() . '/files/seeds';
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rootDir));

        $files = [];
        $thumb_string = '_video_thumb';

        foreach ($rii as $path => $file) {
            if ($file->isDir() || $file->getFileName() == 'hashtags.txt') {
                continue;
            }

            //replace all "\" with "/" and take string between "/files/seeds/" and "/"
            //for example: E:\xampp\htdocs\mimic\public/files/seeds/Beatbox/Beatbom.mp4, you take "Beatbox"
            preg_match('/(?<=\/files\/seeds\/)(.*)(?=\/)/', str_replace("\\", "/", $file->getPathname()), $matches);

            //if you don't fine "_video_thumb" in string include it in array
            if (strpos($file->getFileName(), $thumb_string) === false) {
                $files[$matches[0]][] = $file->getFileName();
            }
        }

        //before moving all files to a directore remove old directory
        $this->delDir(public_path() . '/files/user');

        //main mimic and its creator
        $mainUserId = 1;
        //$dirName = Beatbox, HappyDogs, Muscles...
        foreach ($files as $dirName => $filesTmp) {
            $mimicResponses = [];
            $year = "1970";
            $month = $date = "01";
            foreach ($filesTmp as $arrayKey => $file) {
                //get file info
                $path_parts = pathinfo($file);
                $mime = mime_content_type($rootDir . '/' . $dirName . '/' . $file);

                //main mimic
                if ($arrayKey == 0) {
                    $userIdTmp = $mainUserId;
                } //response mimic
                else {
                    $userIdTmp = rand($mainUserId, $users);
                }

                //copy files to another directory
                $path = public_path() . '/files/user/' . $userIdTmp . '/' . $year . '/' . $month;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $fileName = md5(time() . mt_rand()) . '.' . $path_parts['extension'];
                copy($rootDir . '/' . $dirName . '/' . $file, $path . '/' . $fileName);

                //if this is video file get its thumb, move to another folder and save
                $videoThumbFileName = null;
                if (strpos($mime, 'video') !== false) {
                    $videoThumbFileName = md5(time() . mt_rand()) . '.jpg';
                    $videoThumbFile = $path_parts['filename'] . $thumb_string . ".jpg";
                    copy($rootDir . '/' . $dirName . '/' . $videoThumbFile, $path . '/' . $videoThumbFileName);
                }

                //insert into database
                $data =
                    [
                        'file' => $fileName,
                        'mimic_type' => (strpos($mime, 'image') !== false) ? Mimic::TYPE_PIC : Mimic::TYPE_VIDEO,
                        'upvote' => 123456789,
                        'user_id' => $userIdTmp,
                        'created_at' => "$year-$month-$date 12:00:00",
                        'updated_at' => "$year-$month-$date 12:00:00",
                        'video_thumb' => $videoThumbFileName
                    ];

                if ($arrayKey == 0) {
                    //create mimic
                    $mainMimic = $mimic->create($data);
                    //add hashtags for this mimic
                    $mimic->checkHashtags(file_get_contents($rootDir . '/' . $dirName . '/' . 'hashtags.txt'), $mainMimic);

                } //response mimic
                else {
                    $mimicResponses[] = $data;
                }

            }

            $mainUserId++;
            $mainMimic->mimicResponses()->createMany($mimicResponses);

        }

    }

    /**
     * delete directory recursively
     * @param  string $dir Path to a directory
     * @return bool
     */
    public function delDir($dir)
    {
        if (file_exists($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->delDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
    }
}
