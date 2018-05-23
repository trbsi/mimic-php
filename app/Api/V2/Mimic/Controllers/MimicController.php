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
            throw_exception($e);
        }
    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function listMimics(Request $request)
    {
        return response()->json($this->mimic->getMimics($request, $this->authUser));
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        return response()->json($this->mimic->getPaginatedResponseContent($this->mimicResponse->getMimicResponses($request, $this->authUser)));
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

        $model = ($request->original_mimic_id) ? $this->mimic : $this->mimicResponse;
        $id = ($request->original_mimic_id) ?? $request->response_mimic_id;
        $model = $model->find($id);
        $model->preventMutation = true;

        if ($request->original_mimic_id) {
            $api_call_params = [
                'user_id' => $model->user_id,
                'original_mimic_id' => $model->id,
            ];
        } else {
            $api_call_params = [
                'user_id' => $model->originalMimic->user_id,
                'original_mimic_id' => $model->original_mimic_id,
                'response_mimic_id' => $model->id,
            ];
        }

        DB::beginTransaction();

        //try to upvote
        try {
            $model->increment('upvote');
            $model->userUpvotes()->attach($user->id);
            DB::commit();

            //send push
            $data = [
                'authUser' => $user,
                'parameters' => [
                    'api_call' => app('Dingo\Api\Routing\UrlGenerator')->version('v2')->route('mimic.list', array_merge(['page' => 1], $api_call_params)),
                    'position' => Constants::POSITION_SPLIT_SCREEN,
                ],
            ];

            $this->mimic->sendMimicNotification($model, Constants::PUSH_TYPE_UPVOTE, $data);
            $type = Constants::UPVOTED;
        } //downvote
        catch (\Exception $e) {
            DB::rollBack(); //rollback query inside "try"
            $model->decrement('upvote');
            $model->userUpvotes()->detach($user->id);
            $type = Constants::DOWNVOTED;
        }

        $model = $model->fresh();
        return response()->json([
            'type' => $type,
            'upvotes' => $model->upvote,
        ]);
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
            throw_exception($e);
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
