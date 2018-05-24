<?php
namespace App\Api\V2\User\Traits;

trait UserQueryTrait
{
    /**
     * Return top 10 hashtags
     *
     * @param object $authUser Authenticated user
     * @return collection
     */
    public function getTopTenUsers(object $authUser)
    {
        return $this
        ->select($this->getTable().".*")
        ->selectRaw($this->getIAmFollowingYouQuery($authUser))
        ->selectRaw($this->getIsBlockedQuery($authUser))
        ->orderBy('followers', 'DESC')
        ->orderBy('number_of_mimics', 'DESC')
        ->whereNotIn('id', $authUser->blockedUsers->pluck('id')->toArray())
        ->limit(10)
        ->get();
    }

    /**
     * Get all users blocked by me (logged in user)
     *
     * @return collection
     */
    public function getUsersBlockedByMe()
    {
        return $this->blockedUsers;
    }
}
