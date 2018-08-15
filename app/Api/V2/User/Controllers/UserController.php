<?php

namespace App\Api\V2\User\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\User\Repositories\UserRepository;
use Exception;

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
     * Get user profile and return data
     * @param Request $request
     * @param UserRepository $userRepository
     * @throws Exception
     * @return Response
     */
    public function userProfile(Request $request, UserRepository $userRepository)
    {
        try {
            return $userRepository->getProfile($request, $this->authUser, $this->user);
        } catch (Exception $e) {
            throw_exception($e);
        }
    }

    /**
     * Block user
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @throws Exception
     * @return Response
     */
    public function blockUser(Request $request, UserRepository $userRepository)
    {
        try {
            $response = $userRepository->blockUser($request, $this->authUser);
            return response()->json($response);
        } catch (Exception $e) {
            throw_exception($e);
        }
    }
}
