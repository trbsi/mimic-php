<?php

namespace App\Api\V2\User\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\User\Repositories\Post\BlockUserRepository;
use App\Api\V2\User\Repositories\Delete\DeleteUserRepository;
use Exception;
use App\Api\V2\User\Requests\UserRequest;
use App\Api\V2\User\Repositories\Put\UpdateRepository;
use App\Api\V2\User\Resources\Profile\JsonResource\ProfileResource;

class UserController extends BaseAuthController
{
    /**
     * Update last_seen attribute of a user
     *
     * @param Request $request
     * @return void
     */
    public function updateLastSeen(Request $request)
    {
        $this->authUser->last_seen = date("Y-m-d H:i:s");
        $this->authUser->save();
        return response('', 201);
    }

    /**
     * Block user
     *
     * @param Request $request
     * @param BlockUserRepository $blockUserRepository
     * @throws Exception
     * @return Response
     */
    public function blockUser(Request $request, BlockUserRepository $blockUserRepository)
    {
        try {
            $response = $blockUserRepository->blockUser($request, $this->authUser);
            return response()->json($response);
        } catch (Exception $e) {
            throw_exception($e);
        }
    }

    /**
     * @param  Request              $request
     * @param  DeleteUserRepository $deleteUserRepository
     * @return Response
     */
    public function delete(Request $request, DeleteUserRepository $deleteUserRepository)
    {
        $deleteUserRepository->delete($this->authUser, $request->all());
        return response()->json([], 204);
    }

    /**
     * @param  UserRequest      $request
     * @param  UpdateRepository $updateRepository
     * @return Response
     */
    public function update(UserRequest $request, UpdateRepository $updateRepository)
    {
        $updateRepository->update($this->authUser, $request->all());
        $user = $this->authUser->load(['profile.hashtags']);
        $profile = new ProfileResource($user);
        return response()->json($profile);
    }
}
