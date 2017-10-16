<?php
namespace App\Helpers\Cron;

use Image;
use App\Models\Mimic;
use App\Models\MimicResponse;

class ResizeImages
{   
    /**
     * Fake mimic's user and upvote
     */
    public function resizeImages()
    {
        $query = 'created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)';

        //find all mimics where id is admin id and update user_id and upvote
        $results = Mimic::whereRaw($query)->get();

        foreach ($results as $result) {
            $this->resizeAndLowerQuality($result);
        }

        //find all mimic responses where id is admin id and update user_id and upvote
        $results = MimicResponse::whereRaw($query)->get();

        foreach ($results as $result) {
            $this->resizeAndLowerQuality($result);
        }

    }

    /**
     * Resize and lower quality of an image
     * @param  object $model Mimic or MimicResponse model
     * @param  string $file  This is name of an image to get path to
     */
    private function resizeAndLowerQuality($model, $file = null)
    {
        if($file === null) {
            $tmpFile = $model->file;
        } else {
            $tmpFile = $file;
        }

        $imagePath = $model->getFileOrPath($model->user_id, $tmpFile, $model, false, true);
        $img = Image::make($imagePath);

        // resize the image to a width of 1600 and constrain aspect ratio (auto height)
        // prevent possible upsizing
        $img->resize(1600, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save($imagePath, 60);

        //if there is video thumbnail for video, upload ti also
        if($model->video_thumb && $file === null) {
            $this->resizeAndLowerQuality($model, $model->video_thumb);
        }
    }
}