<?php

namespace App\Api\V1\Controllers\Profile;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use DB;

class FollowController extends BaseAuthController
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Follow or unfollow user
     * @param  Request $requets [description]
     * @return [type]           [description]
     */
    public function followUser(Request $request)
    {
        //get user
        $user = User::find($request->id);

        DB::beginTransaction();
        //try to follow
        try {
            $user->increment('followers');
            $this->authUser->userFollowedBy()->attach($user->id);
            $this->authUser->increment('following');
            DB::commit();
            return response()->json(['type' => 'followed']);
        } //unfollow
        catch (\Exception $e) {
            //rollback query in "try" block
            DB::rollBack();
            $user->decrement('followers');
            $this->authUser->userFollowedBy()->detach($user->id);
            $this->authUser->decrement('following');
            return response()->json(['type' => 'unfollowed']);
        }
    }
}
