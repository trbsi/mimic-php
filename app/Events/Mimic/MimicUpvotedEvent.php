<?php

namespace App\Events\Mimic;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MimicUpvotedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Mimic|MimicResponse
     */
    public $model;

    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param Mimic|MimicResponse $model Upvoted Mimic or response Mimic
     * @param User $user Authenticated user (User who upvoted)
     * @param array $data Array of data from request
     * @return void
     */
    public function __construct(object $model, object $user, array $data)
    {
        $this->model = $model;
        $this->user = $user;
        $this->data = $data;
    }
}
