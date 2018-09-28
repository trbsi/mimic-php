<?php

namespace App\Api\V2\User\Resources\Profile\Repositories\Put;

use App\Api\V2\User\Models\User;
use Exception;
use App\Api\V2\Hashtag\Repositories\Post\CreateHashtagsRepository;
use App\Api\V2\User\Resources\Profile\Models\Profile;
use App\Api\V2\User\Resources\Profile\JsonResource\ProfileResource;

final class UpdateProfileRepository
{
    /**
     * @param CreateHashtagsRepository $createHashtagsRepository
     */
    public function __construct(CreateHashtagsRepository $createHashtagsRepository)
    {
        $this->createHashtagsRepository = $createHashtagsRepository;
    }

    /**
     * @param  User   $authUser
     * @param  array  $data
     * @return ProfileResource
     */
    public function update(User $authUser, array $data): ProfileResource
    {
        try {
            $profile = $authUser->profile()->update($data);
            $this->createHashtagsRepository->extractAndSaveHashtags($data['bio'], $authUser->profile);
            return new ProfileResource($authUser->load(['profile.hashtags']));
        } catch (Exception $e) {
            dd($e->getMessage());
            abort(400, __('api/user/profile/errors.profile_not_updated'));
        }
    }
}
