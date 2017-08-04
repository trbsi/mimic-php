<?php
namespace App\Helpers;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class FileUpload
{
    /**
     * upload file to S3
     * @param  $file File object Access via: $request->file('file_name')
     * @param  $path string any path you want to for S3 or your server
     * @param  $allowType array/string What kind of file to allow to upload
     * @param  $uploadWhere string Where to upload file to, S3 or server?
     * @return string File url
     */
    public function upload($file, $path, $allowType = null, $uploadWhere)
    {
        if ($file) {
            if ($allowType != null) {
                $this->checkFile($allowType, $file);
            }

            switch ($uploadWhere) {
                case 'server':
                    return $this->uploadToServer(public_path() . $path, $file);
                    break;

                case 'aws':
                    return $this->uploadToAws($path, $file);
                    break;

            }
        }

        return NULL;

    }

    /**
     * Upload file to Server
     * @param  Object $file File object
     * @param  $path string any path you want to for S3 or your server
     * @return string ULR to a file
     */
    private function uploadToServer($path, $file)
    {
        try {

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file_name = (md5(time() . mt_rand())) . "." . $file->getClientOriginalExtension();
            $file->move($path, $file_name);

            return $file_name;
        } catch (S3Exception $e) {
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * Upload file to AWS
     * @param  Object $file File object or string path to a file
     * @param  $path string any path you want to for S3 or your server
     * @return string ULR to a file
     */
    private function uploadToAws($path, $file)
    {
        try {
            //this is laravel's objec
            if(is_object($file)) {
                $extension = $file->getClientOriginalExtension();
                $sourceFile = $file->getPathName();
            } 
            //this is string path to a file
            else {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $sourceFile = $file;
            }

            $this->s3client = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-2',
                'http'    => [
                    'verify' => false
                ],
                'credentials' => [
                    'key' => env('AWS_KEY'),
                    'secret' => env('AWS_SECRET'),
                ],
            ]);

            $result = $this->s3client->putObject(array(
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $path . (md5(time() . mt_rand())) . "." . $extension,
                'SourceFile' => $sourceFile,
                'ContentType' => 'text/plain',
                'ACL' => 'public-read',
            ));

            return $result['ObjectURL'];
        } catch (S3Exception $e) {
            abort(500, $e->getMessage());
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * check file and its extenstion
     * @param  $allowType [what kind of file to allow to upload]
     * @param  $file            File object, got through: $request->file('file_name')
     */
    private function checkFile($allowType, $file)
    {
        if (is_array($allowType)) {
            $fileAllowed = false;
            foreach ($allowType as $type) {
                //check if allowed type of a file can be found inside Mime type. If you can find it that means this file is allowed
                if (strpos($file->getMimeType(), $type) !== false) {
                    $fileAllowed = true;
                }
            }
            if ($fileAllowed == false) {
                abort(403, trans('validation.file_should_be_image_video'));
            }

        } else {
            switch ($allowType) {
                case 'image':
                    if (strpos($file->getMimeType(), 'image') === false) {
                        abort(403, $file->getClientOriginalName() . " " . trans('validation.is_not_a_picture'));
                    }
                    break;
                case 'video':
                    if (strpos($file->getMimeType(), 'video') === false) {
                        abort(403, $file->getClientOriginalName() . " " . trans('validation.is_not_a_video'));
                    }
                    break;
            }
        }


    }
}