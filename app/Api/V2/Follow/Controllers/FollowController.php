<?php

namespace App\Api\V2\Follow\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\User\Models\User;
use DB;

class FollowController extends BaseAuthController
{
    /**
     * Follow or unfollow user
     * @param  Request $requets [description]
     * @return [type]           [description]
     */
    public function followUser(Request $request)
    {
        //get user
        $user = User::find($request->id);
        $user->preventMutation = $this->authUser->preventMutation = true;

        DB::beginTransaction();
        //try to follow
        try {
            $user->increment('followers');
            $this->authUser->following()->attach($user->id);
            $this->authUser->increment('following');
            DB::commit();
            return response()->json(['type' => 'followed']);
        } //unfollow
        catch (\Exception $e) {
            //rollback query in "try" block
            DB::rollBack();
            $user->decrement('followers');
            $this->authUser->following()->detach($user->id);
            $this->authUser->decrement('following');
            return response()->json(['type' => 'unfollowed']);
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
