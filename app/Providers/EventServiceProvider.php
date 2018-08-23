<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\User\UserFollowedEvent' => [
            'App\Listeners\User\PushNotifications\SendUserFollowedNotificationListener',
        ],
        'App\Events\Mimic\MimicUpvotedEvent' => [
            'App\Listeners\Mimic\PushNotifications\SendMimicUpvotedNotificationListener',
        ],
        'App\Events\Mimic\MimicCreatedEvent' => [
            'App\Listeners\Mimic\PushNotifications\SendMimicCreatedNotificationListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
