<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Api\V2\Mimic\Models\MimicTaguser;
use App\Api\V2\Mimic\Models\MimicHashtag;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

class CreateMimicRepository
{
    /** @var Mimic|MimicReponse This is created model of Mimic or MimicResponse */
    protected $createdModel;

    /** @var array Holds information about uploaded Mimic file */
    protected $mimicFileInfo;

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
     * @param \App\Api\V2\User\Models\User $user Authenticated user
     * @param array $request This is array of data from request
     * @return boolean|object Return false or single created Mimic|MimicResponse
     */
    public function create($user, $request)
    {
        //init variables
        $model = $this->mimic;
        $responseMimic = false; //is someone posted a response or not
        $relations = ['user', 'hashtags', 'mimicResponses.user'];

        //if this is response mimic upload - init variables
        if (array_key_exists('original_mimic_id', $request)) {
            $model = $this->mimicResponse;
            $this->additionalFields['original_mimic_id'] = $request['original_mimic_id'];
            $responseMimic = true;
            $relations = ['user'];

            $this->checkIfOriginalMimicIsDeleted($request);
        }

        //set uploaded Mimic file information
        $this->setMimicFileInfo($request['mimic_file']);

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
            $this->mimic->saveHashtags(array_get($request, 'hashtags'), $this->createdModel);
            //upload video thumbnail
            $this->uploadVideoThumbnail($request);
            //update user's number of mimics
            $user->preventMutation = true;
            $user->increment('number_of_mimics');
            //send notification to a owner of original mimic that someone post a respons
            if ($responseMimic === true) {
                $this->mimic->sendMimicNotification($this->createdModel->originalMimic, Constants::PUSH_TYPE_NEW_RESPONSE, ['authUser' => $user]);
            }
            //@TODO-TagUsers (still in progress and needs to be tested)
            //$this->mimic->checkTaggedUser($request->usernames, $mimic);
            return $this->mimic->getMimicResponseContent($model->where('id', $this->createdModel->id)->with($relations)->first());
        }

        return false;
    }

    /**
     * Check if original Mimic is deleted
     *
     * @param array $request This is array of data from request
     * @throws Exception If original mimic is delete
     */
    private function checkIfOriginalMimicIsDeleted($request)
    {
        //check if mimic has been deleted
        if (!$this->mimic->find($request['original_mimic_id'])) {
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
     * @param array $request Request object in form of array
     */
    private function uploadVideoThumbnail($request)
    {
        if ($this->createdModel->mimic_type === Mimic::TYPE_VIDEO_STRING
            && array_key_exists('video_thumbnail', $request)) {
            $this->createdModel->video_thumb = $this->fileUpload->upload(
                $request['video_thumbnail'],
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
