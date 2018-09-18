<?php

namespace App\Api\V2\Search\Repositories\Get;

use App\Api\V2\Hashtag\Models\Hashtag;
use App\Api\V2\User\Models\User;

final class SearchRepository
{
    public function __construct(Hashtag $hashtag, User $user)
    {
        $this->hashtag = $hashtag;
        $this->user = $user;
    }

    /**
     * Search for users or hashtags
     *
     * @param array $data Array of data from request
     * @param object $authUser Authenticated user
     * @return void
     */
    public function search(array $data, object $authUser)
    {
        //search hashtags
        if (strlen($data['term']) > 1) {
            if (substr($data['term'], 0, 1) === "#") {
                $table = $this->hashtag->getTable();
                $match = 'name';
                $orderBy = 'popularity';
                $term = $data['term'];
                $model = $this->hashtag;
            } //search users
            elseif (substr($data['term'], 0, 1) === "@") {
                $table = $this->user->getTable();
                $match = $orderBy = 'username';
                $term = substr($data['term'], 1);
                $model = $this->user
                ->select("$table.*")
                ->selectRaw($this->user->getIAmFollowingYouQuery($authUser))
                ->selectRaw($this->user->getIsBlockedQuery($authUser));
            } else {
                return [];
            }
        } else {
            return [];
        }

        return $model
        ->whereRaw("(MATCH($match) AGAINST(? IN BOOLEAN MODE))", [$term."*"])
        ->orderBy($orderBy, 'DESC')
        ->get();
    }
}
