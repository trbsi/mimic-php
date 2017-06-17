<?php

namespace App\Api\V1\Controllers\Mimic;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mimic;
use App\Helpers\FileUploadHelper;
use DB;

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
    public function addMimic(Request $request, FileUploadHelper $fileUpload)
    {
        DB::beginTransaction();
        try
        {
            $file = $request->file('file');
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

            //upload mimic
            //path: files/user/USER_ID/YEAR/
            $file = $fileUpload->upload($file, Mimic::FILE_PATH.$this->authUser->id."/".date("Y")."/");

            //check for hashtags
            $hashtags = $this->mimic->checkTags($request->hashtags);

            //tag users
            $taggedUsers = $this->mimic->checkTaggedUser($request->usernames);

            $this->mimic->create(
            [
                'file' => $file, 
                'mimic_type' => $type, 
                'is_response' => $request->is_response, 
                'user_id' => $this->authUser->id
            ]);

            DB::commit();
            return response()->json(
            [   
                'status' => true,
                'showAlert' => false,
                'message' => 
                [
                    'title' => null,
                    'body' => null
                ]
                'mimic' => $this->mimic,
                'hashtags' => $hashtags,
                'taggedUsers' => $taggedUsers
            ]);

        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(
            [
                'status' => false
                'showAlert' => true,
                'message' =>
                [
                    'title' => trans('core.alert.cant_upload_mimic_title'),
                    'body' => trans('core.alert.cant_upload_mimic_body'),
                ]
            ]);
       }
           
    }

    public function listMimics(Request $request)
    {

    }
        
}
