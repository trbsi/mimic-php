<?php

namespace App\Api\V1\Mimic\Controllers;

use App\Api\V1\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V1\User\Models\User;
use App\Api\V1\Mimic\Models\Mimic;
use App\Api\V1\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Api\V1\Mimic\Models\MimicTaguser;
use App\Api\V1\Mimic\Models\MimicHashtag;
use App\Api\V1\Mimic\Requests\AddMimicRequest;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use DB;
use Validator;

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
            //@TODO REMOVE - fake user
            $user = $this->getUser();
            //@TODO REMOVE - fake user
            
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

                //check if mimic has been deleted
                if (!$this->mimic->find($request->original_mimic_id)) {
                    abort(404, trans('validation.mimic_is_deleted'));
                }
            }

            $file = $request->file('file');
            $mime = $file->getMimeType();

            if (strpos($mime, "video") !== false) {
                $type = Mimic::TYPE_VIDEO;
            } elseif (strpos($mime, "image") !== false) {
                $type = Mimic::TYPE_PIC;
            } else {
                abort(400, trans("validation.file_should_be_image_video"));
            }

            //upload mimic
            //path to upload do: files/user/USER_ID/YEAR/
            $fileName = $fileUpload->upload($file, $this->mimic->getFileOrPath($user->id), ['image', 'video'], 'server');

            if ($mimic = $model->create(
                array_merge([
                    'file' => $fileName,
                    'mimic_type' => $type,
                    'user_id' => $user->id
                ], $additionalFields))
            ) {

                //check for hashtags
                $this->mimic->checkHashtags($request->hashtags, $mimic);

                //update user number of mimics
                $user->increment('number_of_mimics');

                //send notification to a owner of original mimic that someone post a respons
                if ($responseMimic == true) {
                    $this->mimic->sendMimicNotification($mimic->originalMimic, Constants::PUSH_TYPE_NEW_RESPONSE, ['authUser' => $user]);
                }

                //@TODO-TagUsers (still in progress and needs to be tested)
                //$this->mimic->checkTaggedUser($request->usernames, $mimic);

                DB::commit();
                return response()->json($this->mimic->getMimicApiResponseContent($model->where('id', $mimic->id)->with($relations)->first())[0]);
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }

    }

    /**
     * Upload video thumb
     * @param  Request $request
     */
    public function uploadVideoThumb(Request $request, FileUpload $fileUpload)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|image',
        ]);

        if ($validator->fails()) {
            abort(400, trans('validation.file_should_be_image'));
        }

        DB::beginTransaction();
        try {

            //get mimic
            if ($request->original_mimic_id) {
                $model = $this->mimic->find($request->original_mimic_id);
            } else {
                $model = $this->mimicResponse->find($request->response_mimic_id);
            }

            //upload mimic
            //path to upload do: files/user/USER_ID/YEAR/
            $fileName = $fileUpload->upload(
                $request->file('file'),
                $this->mimic->getFileOrPath($model->user_id, null, $model),
                ['image'],
                'server'
            );

            $model->video_thumb = $fileName;
            $model->save();

            DB::commit();
            return response()->json(['success' => true, 'video_thumb_url' => $model->video_thumb_url]);
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
        $mimicsResult = $this->mimic->getMimics($request, $this->authUser);
        $structuredMimics = $this->mimic->getMimicApiResponseContent($mimicsResult);

        return response()->json(
            [
                'count' => $mimicsResult->count(),
                'mimics' => $structuredMimics,
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
        //@TODO REMOVE - fake user
        $user = $this->getUser();
        //@TODO REMOVE - fake user
        
        if ($request->original_mimic_id) {
            $model = $this->mimic;
            $id = $request->original_mimic_id;
        } else {
            $model = $this->mimicResponse;
            $id = $request->response_mimic_id;
        }

        DB::beginTransaction();
        $model = $model->find($id);
        $model->preventMutation = true;

        //try to upvote
        try {
            $model->increment('upvote');
            $model->userUpvotes()->attach($user->id);
            DB::commit();
            $this->mimic->sendMimicNotification($model, Constants::PUSH_TYPE_UPVOTE, ['authUser' => $user]);
            return response()->json(['type' => 'upvoted']);
        } //downvote
        catch (\Exception $e) {
            DB::rollBack(); //rollback query inside "try"
            $model->decrement('upvote');
            $model->userUpvotes()->detach($user->id);
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

        $result = $model->find($id);

        if($result && $result->user_id === $this->authUser->id) {
            $result->delete();
            //decrease number of mimics for this user
            $this->authUser->decrement('number_of_mimics');
            return response()->json(['success' => true]);
   
        } else {
            abort(403, trans('mimic.delete.mimic_not_yours'));
        }

    }

    /**
     * Get user's mimics so he can list them and delete them
     * @param  Request $request
     */
    public function getUserMimics(Request $request)
    {
        if ($request->get_responses && ($request->get_responses === 'true' || $request->get_responses === true)) {
            $model = $this->mimicResponse->with('originalMimic');
        } else {
            $model = $this->mimic;
        }

        if ($request->user_id) {
            $user_id = $request->user_id;
        } else {
            $user_id = $this->authUser->id;
        }

        return response()->json(['mimics' => $model->where('user_id', $user_id)->orderBy('id', 'DESC')->get()]);
    }

    /**
     * Report mimic
     * @param  Request $request [description]
     */
    public function reportMimic(Request $request)
    {
        //$request->original_mimic_id
        return response()->json(['success' => true]);
    }

    /**
     * Fake user if this is my account
     */
    private function getUser()
    {
        if(!in_array($this->authUser->email, ["dario.trbovic@yahoo.com"])) {
            $user = $this->authUser;
        } else {
            if(env('APP_ENV') === 'live') {
                $findUser = (rand(0, 1) === 0) ? rand(1, 95) : rand(119, 225);
            } else {
                $findUser = rand(1, 95);
            }

            $user = $this->user->find($findUser);
        }

        return $user;
    }
}
