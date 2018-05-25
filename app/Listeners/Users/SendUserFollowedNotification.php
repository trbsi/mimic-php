<?php

namespace App\Listeners\Users;

use App\Events\Users\UserFollowedEvent;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

/**
 * Send notification when user follows another user. Send notification to a user who was followed
 */
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
            'api_call_params' => [
                'user_id' => $event->authUser->id,
                'profile_picture' => $event->authUser->profile_picture,
                'username' => $event->authUser->username,
            ],
            'position' => Constants::POSITION_USER_PROFILE,
        ];
        $user_id = $event->followedUser->id; //send notfication to

        SendPushNotification::sendNotification($user_id, $data);
    }
}
