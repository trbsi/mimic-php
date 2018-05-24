<?php

namespace App\Api\V2\Profile\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;
use App\Api\V2\Follow\Models\Follow;
use App\Helpers\Constants;

class ProfileController extends BaseAuthController
{
    /**
     * Get user profile and return data
     * @param  Request $request
     */
    public function userProfile(Request $request)
    {
        //if this is set user is accessing other user's profile
        if ($request->id || $request->user_id) {
            $id = $request->id ?? $request->user_id;
        } //user is accessing his own profile
        else {
            $id = $this->authUser->id;
        }

        $userTable = $this->user->getTable();
        $user = User::select("$userTable.*")
        ->selectRaw("IF(EXISTS(SELECT null FROM " . (new Follow)->getTable() . " WHERE followed_by = " . $this->authUser->id . " AND following = $userTable.id),1,0) AS i_am_following_you")
        ->selectRaw("IF(EXISTS(SELECT null FROM users_blocks_pivot WHERE blocked_by = ".$this->authUser->id." AND user_id = users.id),1,0) AS is_blocked")
        ->find($id);

        if ($user) {
            return $user;
        } else {
            abort(404, trans('core.user.user_not_found'));
        }
    }

    /**
     * Report user
     * @param  Request $request [description]
     */
    public function blockUser(Request $request)
    {
        if ((int)$request->user_id === (int)$this->authUser->id) {
            abort(400, trans('users.cant_block_yourself'));
        }

        try {
            //block
            $this->authUser->blockedUsers()->attach($request->user_id);
            $type = Constants::BLOCKED;
        } catch (\Exception $e) {
            //unblock
            $this->authUser->blockedUsers()->detach($request->user_id);
            $type = Constants::UNBLOCKED;
        }
        return response()->json(['type' => $type]);
    }
}
