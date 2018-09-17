<?php
namespace Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\TestResponse;
class TestCaseHelper
{   
    /**
     * @param  TestResponse $response 
     * @return void                 
     */
    public static function decodeResponse(TestResponse $response)
    {
        return json_decode($response->getContent(), true);
    }

    /**
     * @param  string $path
     * @param  string $fileName
     * @param  string $fileType
     * @return UploadedFile
     */
    public static function returnNewUploadedFile(string $path, string $fileName, string $fileType): UploadedFile
    {
        //create a copy of a file
        $fileNameNew = self::copyFile($path, $fileName);

        return new UploadedFile(
            $path.$fileNameNew,
            $fileNameNew,
            $fileType,
            filesize($path.$fileNameNew),
            null,
            true
        );
    }

    /**
     * @param  string $name
     * @return UploadedFile
     */
    public static function returnFakeFile(string $name): UploadedFile
    {
        return UploadedFile::fake()->create($name, 100);
    }

    /**
     * Create a copy of a file so you don't move original file
     * @param  string $path
     * @param  string $fileName
     * @return string
     */
    private static function copyFile(string $path, string $fileName): string
    {
        $fileInfo = pathinfo($path.$fileName);
        $fileNameNew = sprintf('%s.%s', md5($fileName.time()), $fileInfo['extension']);
        copy($path.$fileName, $path.$fileNameNew);

        return $fileNameNew;
    }
}
