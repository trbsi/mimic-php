<?php
namespace App\Helpers\Cron;

use App\Api\V1\Mimic\Models\Mimic;
use App\Api\V1\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;

/**
 * Used to upload files from server to AWS via CronJob
 */
class UploadToAws
{
    const LIMIT = 3;

    public function __construct(FileUpload $fileUpload, Mimic $mimic)
    {
        $this->fileUpload = $fileUpload;
        $this->mimic = $mimic;
    }

    /**
     * Upload original mimic to aws
     */
    public function uploadOriginalMimicsToAws()
    {
        //get 2 mimics where aws_file is null
        foreach (Mimic::whereNull('aws_file')->limit(self::LIMIT)->get() as $model) {
            $this->upload($model);
        }
    }

    /**
     * Upload response mimic to aws
     */
    public function uploadResponseMimicsToAws()
    {
        //get 2 mimics where aws_file is null
        foreach (MimicResponse::whereNull('aws_file')->limit(self::LIMIT)->get() as $model) {
            $this->upload($model);
        }
    }

    /**
     * Actually upload to AWS
     * 
     * @param  Model $model This is model of Mimic or MimicResponse
     */
    private function upload($model)
    {
        $videoThumbUrl = null;
        
        //resize all images include video thumb
        $this->fileUpload->resizeAndLowerQuality($model);

        $path = $this->mimic->getFileOrPath($model->user_id, null, $model);

        //ltrim($path, "/") - remove "/" from the beginning of a string. That's how upload to AWS works
        $url = $this->fileUpload->upload(public_path() . $path . $model->file, ltrim($path, "/"), null, FileUpload::FILE_UPLOAD_AWS);

        //if there is video thumbnail for video, upload ti also
        if ($model->video_thumb) {
            $videoThumbUrl = $this->fileUpload->upload(public_path() . $path . $model->video_thumb, ltrim($path, "/"), null, FileUpload::FILE_UPLOAD_AWS);
        }

        $model->update(['aws_file' => $url, 'aws_video_thumb' => $videoThumbUrl]);
    }
}
