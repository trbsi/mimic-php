<?php

namespace App\Events\Mimic;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MimicCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var bool
     */
    public $isResponseMimic;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Mimic|MimicResponse
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param bool $isResponseMimic To indicate if this is response mimic or not
     * @param User $user Authenticated user
     * @param Mimic|MimicResponse $model Created Mimic or MimicResponse model
     * @return void
     */
    public function __construct(bool $isResponseMimic, object $user, object $model)
    {
        $this->isResponseMimic = $isResponseMimic;
        $this->user = $user;
        $this->model = $model;
    }
}
