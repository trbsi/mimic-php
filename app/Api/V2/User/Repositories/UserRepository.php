<?php

namespace App\Api\V2\User\Repositories;

use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;
use Exception;
use App\Helpers\Constants;

class UserRepository
{
    /**
     * @param Request $request
     * @param User $authUser
     * @param User $user
     * @throws Exception
     * @return User|null
     */
    public function getProfile(Request $request, User $authUser, User $user): ?User
    {
        //if this is set user is accessing other user's profile
        if ($request->id || $request->user_id) {
            $id = $request->id ?? $request->user_id;
        } //user is accessing his own profile
        else {
            $id = $authUser->id;
        }

        $userTable = $user->getTable();
        $user = User::select("$userTable.*")
        ->selectRaw($user->getIAmFollowingYouQuery($authUser))
        ->selectRaw($user->getIsBlockedQuery($authUser))
        ->find($id);

        if ($user) {
            return $user;
        }
        
        abort(404, trans('core.user.user_not_found'));
    }

    /**
     * @param Request $request
     * @param User $authUser
     * @throws Exception
     * @return array
     */
    public function blockUser(Request $request, User $authUser): array
    {
        if ((int)$request->user_id === (int)$authUser->id) {
            abort(400, trans('users.cant_block_yourself'));
        }

        try {
            //block
            $authUser->blockedUsers()->attach($request->user_id);
            $type = Constants::BLOCKED;
        } catch (Exception $e) {
            //unblock
            $authUser->blockedUsers()->detach($request->user_id);
            $type = Constants::UNBLOCKED;
        }
        return ['type' => $type];
    }
}
