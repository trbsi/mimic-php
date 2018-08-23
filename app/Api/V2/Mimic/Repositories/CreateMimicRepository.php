<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Api\V2\Mimic\Models\MimicTaguser;
use App\Api\V2\Mimic\Models\MimicHashtag;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use App\Events\Mimic\MimicCreatedEvent;

class CreateMimicRepository
{
    /** @var Mimic|MimicReponse This is created model of Mimic or MimicResponse */
    private $createdModel;

    /** @var array Holds information about uploaded Mimic file */
    private $mimicFileInfo;

    public function __construct(
        Mimic $mimic,
        MimicResponse $mimicResponse,
        FileUpload $fileUpload
    ) {
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
        $this->fileUpload = $fileUpload;
        $this->additionalFields = [];
    }

    /**
     * Handle original/response Mimic creation
     *
     * @param User $authUser Authenticated user
     * @param array $data This is array of data from request
     * @return boolean|object Return false or single created Mimic|MimicResponse
     */
    public function create($authUser, $data)
    {
        //init variables
        $model = $this->mimic;
        $isResponseMimic = false; //is someone posted a response or not
        $relations = ['user', 'hashtags', 'responses.user', 'meta'];

        //@TODO REMOVE - fake user
        $user = $this->mimic->getUser($authUser);
        //@TODO REMOVE - fake user

        //if this is response mimic upload - init variables
        if (array_key_exists('original_mimic_id', $data)) {
            $model = $this->mimicResponse;
            $this->additionalFields['original_mimic_id'] = $data['original_mimic_id'];
            $isResponseMimic = true;
            $relations = ['user', 'meta'];

            $this->checkIfOriginalMimicIsDeleted($data);
        }

        //set uploaded Mimic file information
        $this->setMimicFileInfo($data['mimic_file']);

        //create mimic
        $this->createdModel = $model->create(array_merge([
            'mimic_type' => $this->getFileType(),
            'file' => $this->fileUpload->upload(
                $this->mimicFileInfo['file'],
                $this->mimic->getFileOrPath($user->id),
                ['image', 'video'],
                FileUpload::FILE_UPLOAD_SERVER
            ),
            'user_id' => $user->id
        ], $this->additionalFields));

        if ($this->createdModel) {
            //check for hashtags
            $this->mimic->saveHashtags(array_get($data, 'hashtags'), $this->createdModel);

            //upload video thumbnail
            $this->uploadVideoThumbnail($data);

            //update user's number of mimics
            $user->preventMutation = true;
            $user->increment('number_of_mimics');

            //save meta
            $this->createdModel->meta()->create(array_get($data, 'meta'));

            event(new MimicCreatedEvent($isResponseMimic, $user, $this->createdModel));

            //@TODO-TagUsers (still in progress and needs to be tested)
            //$this->mimic->checkTaggedUser($request->usernames, $mimic);
            
            $result = $model->where('id', $this->createdModel->id)->with($relations)->first();
            return $this->mimic->getSingleMimicResponseContent($result);
        }

        return false;
    }

    /**
     * Check if original Mimic is deleted
     *
     * @param array $data This is array of data from request
     * @throws Exception If original mimic is delete
     */
    private function checkIfOriginalMimicIsDeleted($data)
    {
        //check if mimic has been deleted
        if (!$this->mimic->find($data['original_mimic_id'])) {
            abort(404, trans('validation.mimic_is_deleted'));
        }
    }

    /**
     * Get type of uploaded file
     *
     * @param UploadedFile $file This is uploaded file taken via Laravel's class UploadedFile
     * @return integer Type of file: 1|2
     */
    private function getFileType()
    {
        if (strpos($this->mimicFileInfo['mimeType'], 'video') !== false) {
            return Mimic::TYPE_VIDEO;
        } elseif (strpos($this->mimicFileInfo['mimeType'], 'image') !== false) {
            return Mimic::TYPE_PHOTO;
        }

        return $type;
    }

    /**
     * Upload video thumbnail if it exists
     *
     * @param array $data Request object in form of array
     */
    private function uploadVideoThumbnail($data)
    {
        if ($this->createdModel->mimic_type === Mimic::TYPE_VIDEO_STRING
            && array_key_exists('video_thumbnail', $data)) {
            $this->createdModel->video_thumb = $this->fileUpload->upload(
                $data['video_thumbnail'],
                $this->mimic->getFileOrPath($this->createdModel->user_id, null, $this->createdModel),
                ['image'],
                FileUpload::FILE_UPLOAD_SERVER
            );
            $this->createdModel->save();
        }
    }

    /**
     * Set information about uploaded Mimic file
     *
     * @param UploadedFile $mimicFile This is uploaded Mimic file
     * @return void
     */
    private function setMimicFileInfo($mimicFile)
    {
        $this->mimicFileInfo = [
            'file' => $mimicFile,
            'mimeType' => $mimicFile->getMimeType(),
            'extension' => $mimicFile->extension(),
        ];
    }
}
