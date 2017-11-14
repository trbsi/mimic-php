<?php

namespace App\Api\V1\Profile\Controllers;

use App\Api\V1\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V1\User\Models\User;
use App\Api\V1\Follow\Models\Follow;

class ProfileController extends BaseAuthController
{
    /**
     * Get user profile and return data
     * @param  Request $request
     */
    public function userProfile(Request $request)
    {
        try {
            //if this is set user is accessing other user's profile
            if ($request->id) {
                $id = $request->id;
            } //user is accessing his own profile
            else {
                $id = $this->authUser->id;
            }

            $userTable = $this->user->getTable();
            return User::select("$userTable.*")
                ->selectRaw("IF(EXISTS(SELECT null FROM " . (new Follow)->getTable() . " WHERE followed_by = " . $this->authUser->id . " AND following = $userTable.id),1,0) AS i_am_following_you")
                ->find($id);
        } catch (\Exception $e) {
            abort(404, trans('core.user.user_not_found'));
        }
    }

    /**
     * Report user
     * @param  Request $request [description]
     */
    public function blockUser(Request $request)
    {
        //$request->user_id -> who to block
        return response()->json(['success' => true]);
    }
}