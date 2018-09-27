<?php

namespace App\Api\V2\Mimic\Repositories\Post;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Helpers\FileUpload;
use App\Api\V2\Mimic\Models\MimicTaguser;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use App\Events\Mimic\MimicCreatedEvent;
use App\Api\V2\Hashtag\Repositories\Post\CreateHashtagsRepository;

final class CreateMimicRepository
{
    /** @var Mimic|Reponse This is created model of Mimic or Response */
    private $createdModel;

    /** @var array Holds information about uploaded Mimic file */
    private $mimicFileInfo;

    /** @var Mimic */
    private $mimic;

    /** @var Response */
    private $response;

    /** @var FileUpload */
    private $fileUpload;

    /** @var CreateHashtagsRepository */
    private $createHashtagsRepository;

    public function __construct(
        Mimic $mimic,
        Response $response,
        FileUpload $fileUpload,
        CreateHashtagsRepository $createHashtagsRepository
    ) {
        $this->mimic = $mimic;
        $this->response = $response;
        $this->fileUpload = $fileUpload;
        $this->additionalFields = [];
        $this->createHashtagsRepository = $createHashtagsRepository;
    }

    /**
     * Handle original/response Mimic creation
     *
     * @param User $authUser Authenticated user
     * @param array $data This is array of data from request
     * @return boolean|object Return false or single created Mimic|Response
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
            $model = $this->response;
            $this->additionalFields['original_mimic_id'] = $data['original_mimic_id'];
            $isResponseMimic = true;
            $relations = ['user', 'meta'];

            $this->checkIfOriginalMimicIsDeleted($data);
        }

        //set uploaded Mimic file information
        $this->setMimicFileInfo($data['mimic_file']);

        //upload file
        $fileName = $this->fileUpload->upload(
            $this->mimicFileInfo['file'],
            $this->mimic->getFileOrPath($user->id),
            FileUpload::FILE_UPLOAD_SERVER,
            ['image', 'video']            
        );

        //create mimic
        $this->createdModel = $model->create(
            array_merge([
                'mimic_type' => $this->getFileType(),
                'file' => $fileName,
                'user_id' => $user->id,
                'description' => trim(array_get($data, 'description')),
            ], $this->additionalFields)
        );

        if ($this->createdModel) {

            if (!$isResponseMimic) {
                //check for hashtags
                $this->createHashtagsRepository->extractAndSaveHashtags(
                    array_get($data, 'description'), 
                    $this->createdModel
                );
            }

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
                FileUpload::FILE_UPLOAD_SERVER,
                ['image']
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
