<?php
namespace App\Helpers;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class FileUpload
{

    public function __construct()
    {
        $this->s3client = new S3Client([
            'version' => 'latest',
            'region' => 'us-west-2',
            'credentials' => [
                'key' => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
            ],
        ]);
    }

    /**
     * upload file to S3
     * @param  $file            File object, got through: $request->file('file_name')
     * @param  $path [any path you want to for S3]
     * @param  $allow [what kind of file to allow to upload]
     * @return [string]         [file url]
     */
    public function upload($file, $path, $allow = null)
    {
        if ($file) {
            if ($allow != null) {
                $this->checkFile($allow, $file);
            }

            try {
                $result = $this->s3client->putObject(array(
                    'Bucket' => env('AWS_BUCKET'),
                    'Key' => $path . (md5(time() . mt_rand())) . "." . $file->getClientOriginalExtension(),
                    'SourceFile' => $file->getPathName(),
                    'ContentType' => 'text/plain',
                    'ACL' => 'public-read',
                ));

                return $result['ObjectURL'];
            } catch (S3Exception $e) {
                throw new \Exception(trans('validation.error_upload_file'));
            }

        }

        return NULL;

    }

    /**
     * check file and its extenstion
     * @param  $allow [what kind of file to allow to upload]
     * @param  $file            File object, got through: $request->file('file_name')
     */
    private function checkFile($allow, $file)
    {
        switch ($allow) {
            case 'image':
                if (strpos($file->getMimeType(), 'image') === false) {
                    throw new \Exception($file->getClientOriginalName() . " " . trans('validation.is_not_a_picture'));
                }
                break;
            case 'video':
                if (strpos($file->getMimeType(), 'video') === false) {
                    throw new \Exception($file->getClientOriginalName() . " " . trans('validation.is_not_a_video'));
                }
                break;
        }
    }
}