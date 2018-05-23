<?php

namespace App\Listeners;

use App\Events\UserFollowedEvent;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

class SendUserFollowedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(UserFollowedEvent $event)
    {
        $data =
        [
            'badge' => 1,
            'sound' => 'default',
        ];

        $data['title'] = trans('notifications.someone_followed_you_title');
        $data['body'] = trans('notifications.someone_followed_you_body', ['user' => $event->authUser->username]);
        $data['parameters'] = [
            'api_call' => app('Dingo\Api\Routing\UrlGenerator')->version('v2')->route('profile.user', 
            [
                'id' => $event->authUser->id,
            ]),
            'position' => Constants::POSITION_USER_PROFILE,
        ];
        $user_id = $event->followedUser->id; //send notfication to

        SendPushNotification::sendNotification($user_id, $data);
    }
}
