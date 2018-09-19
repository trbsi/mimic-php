<?php

namespace App\Api\V2\Follow\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\Follow\Repositories\Get\FollowUserRepository;
use App\Api\V2\Follow\Repositories\Get\GetFollowersRepository;
use App\Api\V2\Follow\Repositories\Get\GetFollowingsRepository;

class FollowController extends BaseAuthController
{
    /**
     * Follow or unfollow user
     * @param  Request $requets [description]
     * @return [type]           [description]
     */
    public function followUser(Request $request, FollowUserRepository $followUserRepository)
    {
        try {
            $data = $followUserRepository->followUser($request->all(), $this->authUser);
            return response()->json($data);
        } catch (\Exception $e) {
            throw_exception($e);
        }
    }

    /**
     * Get all followers of a specific user
     */
    public function followers(Request $request, GetFollowersRepository $getFollowersRepository)
    {
        if ($request->user_id) {
            $userId = $request->user_id;
        } else {
            $userId = $this->authUser->id;
        }

        $result = $getFollowersRepository->getFollowers($userId, $this->authUser);
        return response()->json($result);
    }

    /**
     * Get all users that current user (user_id) is following
     */
    public function following(Request $request, GetFollowingsRepository $getFollowingsRepository)
    {
        if ($request->user_id) {
            $userId = $request->user_id;
        } else {
            $userId = $this->authUser->id;
        }

        $result = $getFollowingsRepository->getFollowings($userId, $this->authUser);
        return response()->json($result);
    }
}
