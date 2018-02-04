<?php

namespace App\Api\V1\Mimic\Repositories;

use App\Api\V1\Mimic\Models\Mimic;
use App\Api\V1\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Api\V1\Mimic\Models\MimicTaguser;
use App\Api\V1\Mimic\Models\MimicHashtag;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

class CreateMimicRepository
{
    public function __construct(
        Mimic $mimic, 
        MimicResponse $mimicResponse, 
        FileUpload $fileUpload)
    {
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
        $this->fileUpload = $fileUpload;
    }

    /**
     * Handle original/response Mimic creation
     * 
     * @param \App\Api\V1\User\Models\User $user Authenticated user
     * @param array $request This is array of data from request
     * @return boolean|object Return false or single created Mimic|MimicResponse
     */
    public function create($user, $request)
    {
        //init variables
        $model = $this->mimic;
        $additionalFields = [];
        $responseMimic = false; //is someone posted a response or not
        $relations = ['user', 'hashtags', 'mimicResponses.user'];

        //if this is response mimic upload - init variables
        if (array_key_exists('original_mimic_id', $request)) {
            $model = $this->mimicResponse;
            $additionalFields['original_mimic_id'] = $request['original_mimic_id'];
            $responseMimic = true;
            $relations = ['user'];

            $this->checkIfOriginalMimicIsDeleted($request);
        }

        //get file and its type
        $file = $request['file'];
        $type = $this->getFileType($file);

        //upload mimic
        //path to upload to: files/user/USER_ID/YEAR/
        $fileName = $this->fileUpload->upload(
            $file, 
            $this->mimic->getFileOrPath($user->id), 
            ['image', 'video'], 
            'server');

        //create mimic
        $mimic = $model->create(
            array_merge([
                'file' => $fileName,
                'mimic_type' => $type,
                'user_id' => $user->id
            ], $additionalFields)
        );

        if ($mimic) {
            //check for hashtags
            $this->mimic->checkHashtags(array_get($request, 'hashtags'), $mimic);

            //update user number of mimics
            $user->increment('number_of_mimics');

            //send notification to a owner of original mimic that someone post a respons
            if ($responseMimic == true) {
                $this->mimic->sendMimicNotification($mimic->originalMimic, Constants::PUSH_TYPE_NEW_RESPONSE, ['authUser' => $user]);
            }

            //@TODO-TagUsers (still in progress and needs to be tested)
            //$this->mimic->checkTaggedUser($request->usernames, $mimic);

            return $this->mimic->getMimicApiResponseContent($model->where('id', $mimic->id)->with($relations)->first())[0];
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
    private function getFileType($file)
    {
        $mime = $file->getMimeType();

        if (strpos($mime, "video") !== false) {
            return Mimic::TYPE_VIDEO;
        } elseif (strpos($mime, "image") !== false) {
            return Mimic::TYPE_PIC;
        }

        return $type;
    }
}