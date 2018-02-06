<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;

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

    public function __construct(Mimic $mimic, MimicResponse $mimicResponse)
    {
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
    }

    public function deleteMimic($request, $authUser)
    {
        if (array_key_exists('original_mimic_id', $request)) {
            $model = $this->mimic;
            $id = $request['original_mimic_id'];
        } else {
            $model = $this->mimicResponse;
            $id = $request['response_mimic_id'];
        }

        $mode = array_key_exists('mode', $request) ? $request['mode'] : null;

        $result = $model->find($id);

        if($result && ($result->user_id === $authUser->id || $mode === 'admin')) {
            //delete Mimic from disk
            $this->removeMimicFromDisk($result);
            //decrease number of mimics for this user
            $authUser->decrement('number_of_mimics');
            $result->delete();
        } else {
            abort(403, trans('mimic.delete.mimic_not_yours'));
        }
    }

    /**
     * Start with process of removing Mimic files, set relative and absolute file paths
     * 
     * @param Mimic|MimicResponse $model Loaded model from database
     * @return void
     */
    private function removeMimicFromDisk($model)
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