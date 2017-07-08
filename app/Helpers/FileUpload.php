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
                if(is_array($allowType)) {
                    foreach ($allowType as $type) {
                        $this->checkFile($type, $file);
                    }
                } else {
                    $this->checkFile($allowType, $file);
                }
            }

            switch ($uploadWhere) {
                case 'server':
                    return $this->uploadToServer($path, $file);
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
            if(!file_exists($path)) {
                mkdir($path);
            }
            return $file->storeAs($path, (md5(time() . mt_rand())) . "." . $file->getClientOriginalExtension());
        } catch (S3Exception $e) {
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * Upload file to AWS
     * @param  Object $file File object
     * @param  $path string any path you want to for S3 or your server
     * @return string ULR to a file
     */
    private function uploadToAws($path, $file)
    {
        try {

            $this->s3client = new S3Client([
                'version' => 'latest',
                'region' => 'us-west-2',
                'credentials' => [
                    'key' => env('AWS_KEY'),
                    'secret' => env('AWS_SECRET'),
                ],
            ]);

            $result = $this->s3client->putObject(array(
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $path . (md5(time() . mt_rand())) . "." . $file->getClientOriginalExtension(),
                'SourceFile' => $file->getPathName(),
                'ContentType' => 'text/plain',
                'ACL' => 'public-read',
            ));

            return $result['ObjectURL'];
        } catch (S3Exception $e) {
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