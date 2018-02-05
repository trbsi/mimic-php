<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;

class DeleteMimicRepository
{
    /**
     * Absolute and relative path to mimic (original or response) files
     * @var null
     */
    private $absolutePathFile = null;
    private $relativePathFile = null;

    /**
     * Absolute and relative path to video thumbnails (original or response)
     * @var null
     */
    private $absolutePathThumb = null;
    private $relativePathThumb = null;

    public function __construct(Mimic $mimic)
    {
        $this->mimic = $mimic;
    }

    /**
     * Start with process of removing Mimic files, set relative and absolute file paths
     * 
     * @param Mimic|MimicResponse $model Loaded model from database
     * @return void
     */
    public function removeMimicFromDisk($model)
    {
        $this->absolutePathFile = $this->mimic->getFileOrPath($model->user_id, $model->file, $model, false, true);
        $this->relativePathFile = $this->mimic->getFileOrPath($model->user_id, $model->file, $model, false, false);

        if($model->video_thumb) {
            $this->absolutePathThumb = $this->mimic->getFileOrPath($model->user_id, $model->video_thumb, $model, false, true);
            $this->relativePathThumb = $this->mimic->getFileOrPath($model->user_id, $model->video_thumb, $model, false, false);
        }

        $this->removeFromLocalDisk();
    }

    /**
     * Remove Mimic file and video thumbnail from local disk storage
     * @return void
     */
    private function removeFromLocalDisk()
    {
        unlink($this->absolutePathFile);
        if($this->absolutePathThumb) {
            unlink($this->absolutePathThumb);
        }
    }

    /**
     * Remove Mimic file and video thumbnail from AWS
     * @return void
     */
    private function removeFromS3()
    {
        //@TODO
    }

}