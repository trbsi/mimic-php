<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserFollowedEvent
{
    use SerializesModels;

    public $authUser;
    public $followedUser;

    /**
     * Create a new event instance.
     *
     * @param User $authUser Authenticated user
     * @param User $followedUser Followed user
     * @return void
     */
    public function __construct(object $authUser, object $followedUser)
    {
        $this->authUser = $authUser;
        $this->followedUser = $followedUser;
    }
}