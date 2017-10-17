<?php
namespace App\Helpers\Cron;

use App\Models\Mimic;
use App\Models\MimicResponse;
use App\Helpers\FileUpload;

class UploadToAws
{
    const LIMIT = 5;

    /**
     * Upload original mimic to aws
     */
    public function uploadOriginalMimicsToAws()
    {
        $mimic = new Mimic;
        $fileUpload = new FileUpload;

        //get 2 mimics where aws_file is null
        foreach (Mimic::whereNull('aws_file')->limit(self::LIMIT)->get() as $model) {
            $this->upload($model, $mimic, $fileUpload);
        }
    }

    /**
     * Upload response mimic to aws
     */
    public function uploadResponseMimicsToAws()
    {
        $mimic = new Mimic;
        $fileUpload = new FileUpload;

        //get 2 mimics where aws_file is null
        foreach (MimicResponse::whereNull('aws_file')->limit(self::LIMIT)->get() as $model) {
            $this->upload($model, $mimic, $fileUpload);
        }
    }

    /**
     * Actually upload to AWS
     * @param  Model $model This is model of Mimic or MimicResponse
     * @param  Model $mimic Mimic model
     * @param  Model $fileUpload FileUpload model
     */
    private function upload($model, $mimic, $fileUpload)
    {
        $videoThumbUrl = null;
        
        //resize all images include video thumb
        $fileUpload->resizeAndLowerQuality($model);

        $path = $mimic->getFileOrPath($model->user_id, null, $model);

        //ltrim($path, "/") - remove "/" from the beginning of a string. That's how upload to AWS works
        $url = $fileUpload->upload(public_path() . $path . $model->file, ltrim($path, "/"), null, 'aws');

        //if there is video thumbnail for video, upload ti also
        if ($model->video_thumb) {
            $videoThumbUrl = $fileUpload->upload(public_path() . $path . $model->video_thumb, ltrim($path, "/"), null, 'aws');
        }

        $model->update(['aws_file' => $url, 'aws_video_thumb' => $videoThumbUrl]);
    }
}