<?php

namespace App\Api\V2\Mimic\Repositories\Delete;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Api\V2\User\Models\User;
use Illuminate\Support\Facades\Storage;

final class DeleteMimicRepository
{
    private const MODE_ADMIN = 'admin';

    /**
     * Absolute path to mimic and video thumbnail (original or response) files
     * @var string|null
     */
    private $absoluteFilePath = null;
    private $absoluteThumbPath = null;

    /**
     * Relative path to mimic and video thumbnail (original or response) files on AWS
     * @var string|null
     */
    private $relativeAwsFilePath = null;
    private $relativeAwsThumbPath = null;

    public function __construct(Mimic $mimic, Response $response)
    {
        $this->mimic = $mimic;
        $this->response = $response;
    }

    /**
     * Find Mimic or response by id and remove
     *
     * @param  array  $data
     * @param  User   $authUser
     * @return void
     */
    public function deleteSingleMimicOrResponseById(array $data, User $authUser)
    {
        if (array_key_exists('original_mimic_id', $data)) {
            $model = $this->mimic;
            $id = $data['original_mimic_id'];
        } else {
            $model = $this->response;
            $id = $data['response_mimic_id'];
        }

        $result = $model->find($id);
        $this->startDeleteProcess($result, $authUser, $data);
    }


    /**
     * Find user's mimics and responses and remove them
     *
     * @param  User   $authUser
     * @return void
     */
    public function deleteUserMimicsAndResponsesByUser(User $authUser)
    {
        $mimics = $this->mimic->where('user_id', $authUser->id)->get();
        $responses = $this->response->where('user_id', $authUser->id)->get();

        foreach ([$mimics, $responses] as $mimicsAndResponses) {
            foreach ($mimicsAndResponses as $model) {
                $this->startDeleteProcess($model, $authUser);
            }
        }
    }

    /**
     * @param  Mimic|Response $model
     * @param  User   $authUser
     * @param  array  $data
     * @return void
     */
    private function startDeleteProcess(object $model, User $authUser, array $data = [])
    {
        $authUser->preventMutation = true;
        $mode = array_key_exists('mode', $data) ? $data['mode'] : null;

        if ($model && ($model->user_id === $authUser->id || $mode === self::MODE_ADMIN)) {
            //delete Mimic from disk
            $this->removeMimicFromDisk($model);
            //decrease number of mimics for this user
            if ($authUser->number_of_mimics > 0) {
                $authUser->decrement('number_of_mimics');
            }
            $model->delete();
        } else {
            abort(403, trans('mimic.delete.mimic_not_yours'));
        }
    }

    /**
     * Start with process of removing Mimic files, set relative and absolute file paths
     *
     * @param Mimic|Response $model Loaded model from database
     * @return void
     */
    private function removeMimicFromDisk($model)
    {
        //get file paths for local disk
        $this->getFilePathsForLocalDisk($model);
        //remove from local disk
        $this->removeFromLocalDisk();

        if ($model->cloud_file) {
            //get file paths for AWS
            $this->getFilePathsForAws($model);
            //remove from AWS
            $this->removeFromS3();
        }
    }

    /**
     * Get file paths for local disk
     *
     * @param Mimic|Response $model Loaded model from a database
     * @return void
     */
    private function getFilePathsForLocalDisk($model)
    {
        $this->absoluteFilePath = $this->mimic->getFileOrPath($model->user_id, $model->file, $model, false, true);

        if ($model->video_thumb) {
            $this->absoluteThumbPath = $this->mimic->getFileOrPath($model->user_id, $model->video_thumb, $model, false, true);
        }
    }

    /**
     * Get file paths for AWS
     *
     * @param Mimic|Response $model Loaded model from a database
     * @return void
     */
    private function getFilePathsForAws($model)
    {
        //Get path from a url: "https://s3.us-east-2.amazonaws.com/mimic.files.test2/files/user/96/2018/02/bd64074eb9dee10b89e7efa05ad56dc3.jpg"
        //You'll get: "/files/user/96/2018/02/bd64074eb9dee10b89e7efa05ad56dc3.jpg"
        //Remove "/" using ltrim
        preg_match("/(?<=".env('AWS_BUCKET').").+/", $model->cloud_file, $match);
        if (!empty($match)) {
            $this->relativeAwsFilePath = ltrim($match[0], '/');
        }

        if ($model->cloud_video_thumb) {
            preg_match("/(?<=".env('AWS_BUCKET').").+/", $model->cloud_video_thumb, $match);
            if (!empty($match)) {
                $this->relativeAwsThumbPath = ltrim($match[0], '/');
            }
        }
    }

    /**
     * Remove Mimic file and video thumbnail from local disk storage
     * @return void
     */
    private function removeFromLocalDisk()
    {
        try {
            //Remove main file
            if ($this->absoluteFilePath) {
                unlink($this->absoluteFilePath);    
            }
            
            //Remove thumb file
            if ($this->absoluteThumbPath) {
                unlink($this->absoluteThumbPath);
            }
        } catch (\Exception $e) {
            //@TODO - log error - $e->getMessage()
        }
    }

    /**
     * Remove Mimic file and video thumbnail from AWS
     * @return void
     */
    private function removeFromS3()
    {
        //Remove main file
        if ($this->relativeAwsFilePath) {
            Storage::cloud()->delete($this->relativeAwsFilePath);            
        }

        //Remove thumb file
        if ($this->relativeAwsThumbPath) {
            Storage::cloud()->delete($this->relativeAwsThumbPath);
        }
    }
}
