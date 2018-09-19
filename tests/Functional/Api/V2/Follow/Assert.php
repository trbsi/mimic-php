<?php
namespace Tests\Functional\Api\V2\Follow;

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
            case 'followers':
            case 'followings':
                return $this->getFollowersOrFollowingJsonStructureOnSuccess($type);
            case 'followed':
            case 'unfollowed':
                return $this->getFollowOrUnfollowJsonStructureOnSuccess();
        }
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {
        switch ($type) {
            case 'followers':
            case 'followings':
                return $this->getFollowersOrFollowingJsonOnSuccess($data, $type);
            case 'followed':
            case 'unfollowed':
                return $this->getFollowedOrUnfollowedJsonOnSuccess($data);
        }
    }

    /**
     * @param  string $type Values: followings, followers
     * @return array
     */
    private function getFollowersOrFollowingJsonStructureOnSuccess(string $type): array
    {
        return [
            'meta' => [
                'pagination' => [
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ],
            $type => [
                '*' => [
                    'id',
                    'username',
                    'i_am_following_you',
                    'is_blocked',
                    'profile_picture',
                    'followers',
                    'number_of_mimics',
                ]
            ]
        ];
    }

    /**
     * @param  array $data
     * @param  string $type Values: followings, followers
     * @return array
     */
    private function getFollowersOrFollowingJsonOnSuccess(array $data, string $type): array
    {
        return [
            'meta' => [
                'pagination' => $data['pagination']
            ],
            $type => $data[$type]
        ];
    }

    /**
     * @return array
     */
    private function getFollowOrUnfollowJsonStructureOnSuccess(): array
    {
        return [
            'type',
            'followers',
        ];
    }

    /**
     * @param  array  $data
     * @return array
     */
    private function getFollowedOrUnfollowedJsonOnSuccess(array $data): array
    {
        return [
            'type' => $data['type'],
            'followers' => $data['followers'],
        ];
    }
}
