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
            if ($request->original_mimic_id) {
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
            //path to upload do: files/user/USER_ID/YEAR/
            $fileName = $fileUpload->upload($file, $this->mimic->getFileOrPath($this->authUser->id), ['image', 'video'], 'server');

            if ($mimic = $model->create(
                array_merge([
                    'file' => $fileName,
                    'mimic_type' => $type,
                    'user_id' => $this->authUser->id
                ], $additionalFields))
            ) {

                //check for hashtags
                $this->mimic->checkHashtags($request->hashtags, $mimic);

                //update user number of mimics
                $this->authUser->increment('number_of_mimics');

                //send notification to a owner of original mimic that someone post a respons
                if ($responseMimic == true) {
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
        
        return response()->json(
        [
            'count' => $this->mimic->getMimicCount($request),
            'mimics' => $this->mimic->getMimicApiResponseContent($mimics),
        ]);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $mimicsResponses = $this->mimicResponse->getMimicResponses($request, $this->authUser);

        return response()->json(['mimics' => $this->mimic->getMimicApiResponseContent($mimicsResponses, true)]);
    }

    /**
     * Upvote original or response mimic
     * @param  Request $request
     */
    public function upvote(Request $request)
    {
        if ($request->original_mimic_id) {
            $model = $this->mimic;
            $id = $request->original_mimic_id;
        } else {
            $model = $this->mimicResponse;
            $id = $request->response_mimic_id;
        }
        DB::beginTransaction();
        $model = $model->find($id);
        //try to upvote
        try {
            $model->increment('upvote');
            $model->userUpvotes()->attach($this->authUser->id);
            DB::commit();
            return response()->json(['type' => 'upvoted']);
        } //downvote
        catch (\Exception $e) {
            DB::rollBack(); //rollback query inside "try"
            $model->decrement('upvote');
            $model->userUpvotes()->detach($this->authUser->id);
            return response()->json(['type' => 'downvoted']);
        }
    }

    /**
     * Delete original or response mimic
     * @param  Request $request
     */
    public function delete(Request $request)
    {
        if ($request->original_mimic_id) {
            $model = $this->mimic;
            $id = $request->original_mimic_id;
        } else {
            $model = $this->mimicResponse;
            $id = $request->response_mimic_id;
        }

        $model->find($id)->delete();
        return response()->json(['success' => true]);   
    }

    /**
     * Get user's mimics so he can list them and delete them
     * @param  Request $request
     */
    public function getUserMimics(Request $request)
    {
        if($request->get_responses) {
            $model = $this->mimicResponse->with('originalMimic');
        } else {
            $model = $this->mimic;
        }

        if($request->user_id) {
            $user_id = $request->user_id;
        } else {
            $user_id = $this->authUser->id;
        }

        return response()->json(['mimics' => $model->where('user_id', $user_id)->orderBy('id', 'DESC')->get()]);   
    }
}
