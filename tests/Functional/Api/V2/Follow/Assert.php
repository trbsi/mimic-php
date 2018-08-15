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
            case 'following':
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
            case 'following':
                return $this->getFollowersOrFollowingJsonOnSuccess($data, $type);
            case 'followed':
            case 'unfollowed':  
                return $this->getFollowedOrUnfollowedJsonOnSuccess($data);
        }
	}

    /**
     * @param  string $type Values: following, followers
     * @return array
     */
    private function getFollowersOrFollowingJsonStructureOnSuccess(string $type): array
    {
        return [
            $type => [
                '*' => [
                    'id',
                    'email',
                    'username',
                    'profile_picture',
                    'followers',
                    'following',
                    'number_of_mimics',
                    'created_at',
                    'updated_at',
                    'pivot' => [
                        'following',
                        'followed_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ];
    }

    /**
     * @param  array $data
     * @param  string $type Values: following, followers
     * @return array
     */
    private function getFollowersOrFollowingJsonOnSuccess(array $data, string $type): array
    {
        return [
            $type => [
                [
                    'id' => $data['id'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'profile_picture' => $data['profile_picture'],
                    'followers' => $data['followers'],
                    'following' => $data['following'],
                    'number_of_mimics' => $data['number_of_mimics'],
                    'pivot' => [
                        'followed_by' => $data['pivot_followed_by'],
                        'following' => $data['pivot_following'],
                    ]
                ]
            ]
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