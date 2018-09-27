<?php
namespace App\Cron;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Helpers\FileUpload;

/**
 * Used to upload files from server to AWS via CronJob
 */
class UploadToAws
{
    const LIMIT = 1;

    /**
     * @param FileUpload $fileUpload
     * @param Mimic $mimic
     */
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
        $mimics = Mimic::whereNull('aws_file')->limit(self::LIMIT)->get();
        foreach ($mimics as $model) {
            $this->upload($model);
        }
    }

    /**
     * Upload response mimic to aws
     */
    public function uploadResponseMimicsToAws()
    {
        //get 2 mimics where aws_file is null
        $responses = Response::whereNull('aws_file')->limit(self::LIMIT)->get();
        foreach ($responses as $model) {
            $this->upload($model);
        }
    }

    /**
     * Actually upload to AWS
     *
     * @param  Model $model This is model of Mimic or Response
     */
    private function upload($model)
    {
        $videoThumbUrl = null;
        
        //Resize and upload original
        $image = $this->fileUpload->resizeAndLowerQuality($model, $model->file);
        $absolutePath = $this->mimic->getAbsolutePathToFile($model->user_id, $model->file, $model);
        $relativePath = $this->mimic->getRelativePathWithoutFile($model->user_id, $model);
        $url = $this->fileUpload->upload(
            $absolutePath,
            $relativePath,
            FileUpload::FILE_UPLOAD_AWS,
            null
        );

        if ($image) {
            $model->meta()->update([
                'height' => $image->height(),
                'width' => $image->width(),
            ]);
        }

        //Resize and upload thumbnail
        if ($model->video_thumb) {
            $image = $this->fileUpload->resizeAndLowerQuality($model, $model->video_thumb);
            $absolutePath = $this->mimic->getAbsolutePathToFile($model->user_id, $model->video_thumb, $model);
            $relativePath = $this->mimic->getRelativePathWithoutFile($model->user_id, $model);
            $videoThumbUrl = $this->fileUpload->upload(
                $absolutePath,
                $relativePath,
                FileUpload::FILE_UPLOAD_AWS,
                null
            );
            
            if ($image) {
                $model->meta()->update([
                    'thumbnail_height' => $image->height(),
                    'thumbnail_width' => $image->width(),
                ]);
            }
        }

        $model->update(['aws_file' => $url, 'aws_video_thumb' => $videoThumbUrl]);
    }
}
