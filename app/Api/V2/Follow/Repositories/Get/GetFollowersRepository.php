<?php

namespace App\Api\V2\Follow\Repositories\Get;

use App\Api\V2\User\Models\User;
use App\Api\V2\Follow\JsonResources\FollowersResource;
use App\Helpers\Traits\PaginationTrait;
use App\Api\V2\Follow\Models\Follow;

final class GetFollowersRepository
{
	use PaginationTrait;

	private const PAGINATION = 30;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Follow
	 */
	private $follow;

	/**
	 * @param User   $user   
	 * @param Follow $follow 
	 */
	public function __construct(User $user, Follow $follow)
	{
		$this->user = $user;
		$this->follow = $follow;
	}

	/**
	 * @param  int    $userId   
	 * @param  User   $authUser 
	 * @return array           
	 */
	public function getFollowers(int $userId, User $authUser): array
	{
        $result = $this->user->find($userId)
        ->followers()
        ->select($this->user->getTable().'.*')
        ->selectRaw($this->user->getIAmFollowingYouQuery($authUser))
        ->selectRaw($this->user->getIsBlockedQuery($authUser))
        ->orderBy($this->follow->getTable().'.id', 'DESC')
        ->paginate(self::PAGINATION);

        return [
			'meta' => $this->getPagination($result),
			'followers' => FollowersResource::collection($result),
		];
	}
}