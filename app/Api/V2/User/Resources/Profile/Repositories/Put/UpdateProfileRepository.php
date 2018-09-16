<?php

namespace App\Api\V2\User\Resources\Profile\Repositories\Put;

use App\Api\V2\User\Models\User;
use Exception;

final class UpdateProfileRepository
{
    /**
     * @param  User   $authUser
     * @param  array  $data
     * @return void
     */
    public function update(User $authUser, array $data): void
    {
        try {
            $authUser->profile()->updateOrCreate(['user_id' => $authUser->id], $data);
        } catch (Exception $e) {
            abort(400, __('api/user/profile/errors.profile_not_updated'));
        }
    }
}
