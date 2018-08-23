<?php

namespace App\Api\V2\Follow\Repositories;

use App\Api\V2\User\Models\User;
use DB;
use App\Helpers\Constants;
use App\Events\User\UserFollowedEvent;

class FollowUserRepository
{
    /**
     * Follow or unfollow user
     * @param  array $data Array of data from request
     * @param  object $authUser Authenticated user
     * @return array
     */
    public function followUser(array $data, object $authUser): array
    {
        //get user
        $user = User::find($data['id']);
        if (!$user) {
            abort(400, trans('users.user_not_found'));
        }

        if ((int)$authUser->id === (int)$user->id) {
            abort(400, trans('users.cant_follow_yourself'));
        }

        $user->preventMutation = $authUser->preventMutation = true;
        DB::beginTransaction();

        try {
            //follow
            $user->increment('followers');
            $authUser->following()->attach($user->id);
            $authUser->increment('following');
            DB::commit();
            $type = Constants::FOLLOWED;
            event(new UserFollowedEvent($authUser, $user));
        } catch (\Exception $e) {
            //unfollow
            DB::rollBack();
            $user->decrement('followers');
            $authUser->following()->detach($user->id);
            $authUser->decrement('following');
            $type = Constants::UNFOLLOWED;
        }

        return [
            'type' => $type,
            'followers' => $user->fresh()->followers,
        ];
    }
}
