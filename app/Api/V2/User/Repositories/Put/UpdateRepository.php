<?php

namespace App\Api\V2\User\Repositories\Put;

use App\Api\V2\User\Models\User;
use App\Api\V2\User\Resources\Profile\JsonResource\ProfileResource;

final class UpdateRepository
{
    /**
     * @param  User   $authUser
     * @param  array  $data
     * @return ProfileResource
     */
    public function update(User $authUser, array $data): ProfileResource
    {
        $authUser->update($data);
        $user = $authUser->load(['profile.hashtags']);
        return new ProfileResource($user);
    }
}
