<?php

namespace App\Api\V2\User\Resources\Profile\Repositories\Get;

use App\Api\V2\User\Models\User;
use Exception;
use App\Helpers\Constants;
use App\Api\V2\User\Resources\Profile\JsonResource\ProfileResource;

final class GetProfileRepository
{
    /**
     * @param array $data
     * @param User $authUser
     * @param User $user
     * @return ProfileResource
     */
    public function getProfile(array $data, User $authUser, User $user): ProfileResource
    {
        //if this is set user is accessing other user's profile
        if (array_key_exists('id', $data) || array_key_exists('user_id', $data)) {
            $id = array_get($data, 'id') ?? array_get($data, 'user_id');
        } //user is accessing his own profile
        else {
            $id = $authUser->id;
        }

        $userTable = $user->getTable();
        $user = User::select("$userTable.*")
        ->selectRaw($user->getIAmFollowingYouQuery($authUser))
        ->selectRaw($user->getIsBlockedQuery($authUser))
        ->with(['profile.hashtags'])
        ->find($id);

        if ($user) {
            return new ProfileResource($user);
        }
        
        abort(404, trans('core.user.user_not_found'));
    }
}
