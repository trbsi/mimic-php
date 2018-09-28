<?php

namespace App\Api\V2\Mimic\Repositories\Get;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\JsonResources\UpvotesResource;
use App\Helpers\Traits\PaginationTrait;
use App\Helpers\Constants;

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
     * @param Mimic    $mimic
     * @param Response $response
     * @param User     $user
     */
    public function __construct(Mimic $mimic, Response $response, User $user)
    {
        $this->mimic = $mimic;
        $this->response = $response;
        $this->user = $user;
    }

    /**
     * @param  int    $id
     * @param  User   $authUser
     * @param  string $type Which type of mimic do you need: original, response
     * @return array
     */
    public function getUpvotes(int $id, User $authUser, string $type): array
    {
        $model = $type === Constants::MIMIC_ORIGINAL ? $this->mimic : $this->response;
        $upvoteTable = $type === Constants::MIMIC_ORIGINAL ? db_table('mimic_upvote') : db_table('mimic_response_upvote');

        $upvotes = $model
        ->find($id)
        ->upvotes()
        ->select($this->user->getTable().'.*')
        ->selectRaw($this->user->getIAmFollowingYouQuery($authUser))
        ->selectRaw($this->user->getIsBlockedQuery($authUser))
        ->orderBy($upvoteTable.'.id', 'DESC')
        ->paginate(self::PAGINATION);

        return [
            'meta' => $this->getPagination($upvotes),
            'upvotes' => UpvotesResource::collection($upvotes),
        ];
    }
}
