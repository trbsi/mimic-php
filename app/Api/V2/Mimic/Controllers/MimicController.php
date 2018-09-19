<?php

namespace App\Api\V2\Mimic\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Helpers\FileUpload;
use App\Api\V2\Mimic\Requests\CreateMimicRequest;
use App\Helpers\Constants;
use App\Api\V2\Mimic\Repositories\Post\CreateMimicRepository;
use App\Api\V2\Mimic\Repositories\Delete\DeleteMimicRepository;
use App\Api\V2\Mimic\Repositories\Post\UpvoteMimicRepository;
use App\Api\V2\Mimic\Repositories\Get\ReadMimicRepository;
use DB;
use Validator;
use Exception;

class MimicController extends BaseAuthController
{
    public function __construct(
        User $user,
        Mimic $mimic,
        Response $response
    ) {
        parent::__construct($user);
        $this->mimic = $mimic;
        $this->response = $response;
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
            $result = $createMimicRepository->create($this->authUser, $request->all());

            if ($result) {
                DB::commit();
                return response()->json($result);
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (Exception $e) {
            DB::rollBack();
            throw_exception($e);
        }
    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function getMimics(Request $request)
    {
        $mimics = $this->mimic->getMimics($request, $this->authUser);
        $result = $this->mimic->getPaginatedResponseContent($mimics);
        return response()->json($result);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $responses = $this->response->getMimicResponses($request, $this->authUser);
        $result = $this->mimic->getPaginatedResponseContent($responses);
        return response()->json($result);
    }

    /**
     * Upvote original or response mimic
     * @param  Request $request
     */
    public function upvote(Request $request, UpvoteMimicRepository $upvoteMimicRepository)
    {
        $data = $upvoteMimicRepository->upvote($request->all(), $this->authUser);
        return response()->json($data);
    }

    /**
     * Delete original or response mimic
     * @param  Request $request
     */
    public function delete(Request $request, DeleteMimicRepository $deleteMimicRepository)
    {
        DB::beginTransaction();
        try {
            $deleteMimicRepository->deleteSingleMimicOrResponseById($request->all(), $this->authUser);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw_exception($e);
        }
    }

    /**
     * Get user's mimics so he can list them and delete them
     * @param  Request             $request
     * @param  ReadMimicRepository $readMimicRepository
     * @return Response
     */
    public function getUserMimics(Request $request, ReadMimicRepository $readMimicRepository)
    {
        try {
            $result = $readMimicRepository->getUserMimics($request, $this->authUser);
            return response()->json(['mimics' => $result]);
        } catch (\Exception $e) {
            throw_exception($e);
        }
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
