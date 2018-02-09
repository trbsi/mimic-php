<?php
namespace App\Helpers;

use Aws\S3\S3Client;

class AwsHelper
{
    /**
     * Init AWS, create AWS object
     * @return S3Client 
     */
    public function initAwsS3client()
    {
        return 
        new S3Client([
            'version' => 'latest',
            'region' => 'us-east-2',
            'http' => [
                'verify' => false
            ],
            'credentials' => [
                'key' => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
            ],
        ]);
    }
}
