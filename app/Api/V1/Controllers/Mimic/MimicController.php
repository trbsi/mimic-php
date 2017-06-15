<?php

namespace App\Api\V1\Controllers\Mimic;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mimic;
use App\Helpers\FileUploadHelper;

class MimicController extends BaseAuthController
{
    public function __construct(User $user, Mimic $mimic)
    {
        parent::__construct($user);
        $this->mimic = $mimic;
    }

    /**
     * Add new mimic
     * @param Request $request
     */
    public function addMimic(Request $request)
    {
        $media = $request->file('media');
        $mime =  $media->getMimeType();

        if(strpos($mime,"video") !== false)
        {
            $type = Mimic::TYPE_VIDEO;
        }
        elseif(strpos($mime,"image") !== false)
        {
            $type = Mimic::TYPE_PIC;
        }
        else{
            throw new \Exception(trans("validation.file_should_be_image_video"), 403);
        }

        $this->mimic->create(
        [
            'media' => $media, 
            'mimic_type' => $type, 
            'is_response' => $request->is_response, 
            'is_private' => $request->is_private, 
            'user_id' => $this->authUser->id
        ]);
           
    }

    public function listMimics(Request $request)
    {

    }
}
