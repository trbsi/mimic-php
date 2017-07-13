<?php

namespace App\Api\V1\Controllers\Mimic;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mimic;
use App\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Models\MimicTaguser;
use App\Models\MimicHashtag;
use App\Api\V1\Requests\Mimic\AddMimicRequest;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use DB;

class MimicController extends BaseAuthController
{
    public function __construct(User $user,
                                Mimic $mimic,
                                MimicResponse $mimicResponse,
                                MimicTaguser $mimicTaguser,
                                MimicHashtag $mimicHashtag)
    {
        parent::__construct($user);
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
        $this->mimicTaguser = $mimicTaguser;
        $this->mimicHashtag = $mimicHashtag;
    }

    /**
     * Add new mimic
     * @param Request $request
     */
    public function addMimic(AddMimicRequest $request, FileUpload $fileUpload)
    {
        DB::beginTransaction();
        try {

            //init variables
            $model = $this->mimic;
            $additionalFields = [];
            $responseMimic = false; //is someone posted a response or not
            $relations = ['user', 'hashtags', 'mimicResponses.user'];

            //if this is response upload
            if($request->original_mimic_id) {
                $model = $this->mimicResponse;
                $additionalFields['original_mimic_id'] = $request->original_mimic_id;
                $relations = ['user'];
                $responseMimic = true;
            } 

            $file = $request->file('file');
            $mime = $file->getMimeType();

            if (strpos($mime, "video") !== false) {
                $type = Mimic::TYPE_VIDEO;
            } elseif (strpos($mime, "image") !== false) {
                $type = Mimic::TYPE_PIC;
            } else {
                abort(403, trans("validation.file_should_be_image_video"));
            }

            //upload mimic
            //path: files/user/USER_ID/YEAR/
            $file = $fileUpload->upload($file, $this->mimic->getFileOrPath($this->authUser), ['image', 'video'], 'server');

            if ($mimic = $model->create(
                array_merge([
                    'file' => $file,
                    'mimic_type' => $type,
                    'user_id' => $this->authUser->id
                ],$additionalFields))
            ) {

                //check for hashtags
                $this->mimic->checkTags($request->hashtags, $mimic);

                //update user number of mimics
                $this->authUser->increment('number_of_mimics');

                //send notification to a owner of original mimic that someone post a respons
                if($responseMimic == true) {
                    $this->mimic->sendMimicNotification($mimic->mimic, Constants::PUSH_TYPE_NEW_RESPONSE);
                }
                
                 
                //@TODO-TagUsers (still in progress and needs to be tested)
                //$this->mimic->checkTaggedUser($request->usernames, $mimic);

                DB::commit();
                return response()->json(
                    [
                        'mimics' => $this->mimic->getMimicApiResponseContent($model->where('id', $mimic->id)->with($relations)->first())
                    ]
                );
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }

    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function listMimics(Request $request)
    {
        $mimics = $this->mimic->getMimics($request, $this->authUser);

        return response()->json(['mimics' => $this->mimic->getMimicApiResponseContent($mimics)]);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $mimicsResponses = $this->mimicResponse->getMimicResponses($request, $this->authUser);

        return response()->json(['mimics' => $mimicsResponses]);
    }

    /**
     * Upvote original or response mimic
     * @param  Request $request
     */
    public function upvote(Request $request)
    {
        if($request->original_mimic_id) {
            $model = $this->mimic;
            $id = $request->original_mimic_id;
        } else {
            $model = $this->mimicResponse;
            $id = $request->reseponse_mimic_id;
        }
        DB::beginTransaction();
        $model = $model->find($id);
        //try to increment, if you can't catch and decrement it
        try {
            $model->increment('upvote');
            $model->upvotes()->create(['user_id' => $this->authUser->id]);
            DB::commit();
            return response()->json(['type' => 'voted']);
        } catch (\Exception $e) {
            DB::rollBack(); //rollback query inside "try"
            $model->decrement('upvote');
            $model->upvotes()->where(['user_id' => $this->authUser->id])->delete();
            return response()->json(['type' => 'downvoted']);

        }
    }
}
