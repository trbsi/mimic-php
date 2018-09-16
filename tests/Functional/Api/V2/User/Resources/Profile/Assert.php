<?php

namespace Tests\Functional\Api\V2\User\Resources\Profile;

use Tests\Assert\AssertInterface;
use Tests\Assert\AssertAbstract;

class Assert extends AssertAbstract implements AssertInterface
{
    /**
     * @inheritdoc
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array
    {
        switch ($type) {
            case 'profile':
                return $this->getProfileJsonStructureOnSuccess();
        }
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {
        switch ($type) {
            case 'profile':
                return $this->getProfileJsonOnSuccess($data);
        }
    }

    /**
     * @return array
     */
    private function getProfileJsonStructureOnSuccess(): array
    {
        return [
            'id',
            'email',
            'username',
            'profile_picture',
            'followers',
            'following' ,
            'number_of_mimics',
            'created_at',
            'updated_at',
            'i_am_following_you',
            'is_blocked',
            'profile' => [
                'bio',
                'hashtags',
            ]
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function getProfileJsonOnSuccess(array $data): array
    {
        return [
            'id' => $data['id'],
            'email' => $data['email'],
            'username' => $data['username'],
            'profile_picture' => $data['profile_picture'],
            'followers' => $data['followers'],
            'following' => $data['following'],
            'number_of_mimics' => $data['number_of_mimics'],
            'i_am_following_you' => $data['i_am_following_you'],
            'is_blocked' => $data['is_blocked'],
            'profile' => [
                'bio' => $data['profile']['bio'],
                'hashtags' => $data['profile']['hashtags'],
            ]
        ];
    }
}
