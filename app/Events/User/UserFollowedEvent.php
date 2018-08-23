<?php

namespace App\Events\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserFollowedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $authUser;

    /**
     * @var User
     */
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
