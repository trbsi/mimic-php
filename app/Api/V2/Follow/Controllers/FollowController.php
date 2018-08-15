<?php

namespace App\Api\V2\Follow\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\User\Models\User;
use DB;
use App\Helpers\Constants;
use App\Api\V2\Follow\Repositories\FollowUserRepository;

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
    public function followers(Request $request, User $user)
    {
        if ($request->user_id) {
            $user_id = $request->user_id;
        } else {
            $user_id = $this->authUser->id;
        }

        return response()->json(['followers' => $user->find($user_id)->followers()->get()]);
    }

    /**
     * Get all users that current user (user_id) is following
     */
    public function following(Request $request, User $user)
    {
        if ($request->user_id) {
            $user_id = $request->user_id;
        } else {
            $user_id = $this->authUser->id;
        }

        return response()->json(['following' => $user->find($user_id)->following()->get()]);
    }
}
