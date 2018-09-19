<?php

namespace App\Api\V2\Mimic\Repositories\Get;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\JsonResources\UpvotesResource;
use App\Helpers\Traits\PaginationTrait;

final class GetUpvotesRepository
{
	use PaginationTrait;

	private const PAGINATION = 30;

	/**
	 * @var Mimic
	 */
	private $mimic;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @param Mimic $mimic
	 */
	public function __construct(Mimic $mimic, User $user) 
	{
		$this->mimic = $mimic;
		$this->user = $user;
	}

	/**
	 * @param  int    $id       
	 * @param  User   $authUser 
	 * @return array           
	 */
	public function getUpvotes(int $id, User $authUser): array
	{
		$upvotes = $this->mimic
		->find($id)
		->upvotes()
		->select($this->user->getTable().'.*')
		->selectRaw($this->user->getIAmFollowingYouQuery($authUser))
		->selectRaw($this->user->getIsBlockedQuery($authUser))
		->orderBy('mimic_upvote.id', 'DESC')
		->paginate(self::PAGINATION);

		return [
			'meta' => $this->getPagination($upvotes),
			'upvotes' => UpvotesResource::collection($upvotes),
		];
	}
}