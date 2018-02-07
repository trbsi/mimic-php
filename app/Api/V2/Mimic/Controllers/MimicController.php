<?php

namespace App\Api\V2\Mimic\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Helpers\FileUpload;
use App\Api\V2\Mimic\Requests\CreateMimicRequest;
use App\Helpers\Constants;
use App\Api\V2\Mimic\Repositories\CreateMimicRepository;
use App\Api\V2\Mimic\Repositories\DeleteMimicRepository;
use DB;
use Validator;

class MimicController extends BaseAuthController
{
    public function __construct(
        User $user,
                                Mimic $mimic,
                                MimicResponse $mimicResponse
    ) {
        parent::__construct($user);
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
    }

    /**
     * Add new mimic
     *
     * @param CreateMimicRequest $request Laravel's custom reuest
     * @param CreateMimicRepository $createMimicRepository Repository for handling creation
     */
    public function createMimic(CreateMimicRequest $request, CreateMimicRepository $createMimicRepository)
    {
        DB::beginTransaction();
        try {
            //@TODO REMOVE - fake user
            $user = $this->mimic->getUser($this->authUser, $this->user);
            //@TODO REMOVE - fake user
            
            $result = $createMimicRepository->create($user, $request->all());

            if ($result) {
                DB::commit();
                return response()->json($result);
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (\Exception $e) {
            DB::rollBack();
            abort(method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode(), $e->getMessage());
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
                FileUpload::FILE_UPLOAD_SERVER
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
            ]
        );
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
        $user = $this->mimic->getUser($this->authUser, $this->user);
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
    public function delete(Request $request, DeleteMimicRepository $deleteMimicRepository)
    {
        DB::beginTransaction();
        try {
            $deleteMimicRepository->deleteMimic($request->all(), $this->authUser);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            abort(method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode(), $e->getMessage());
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
        return response()->json(['success' => true, 'message' => trans('mimic.report.mimic_has_been_reported')]);
    }
}
