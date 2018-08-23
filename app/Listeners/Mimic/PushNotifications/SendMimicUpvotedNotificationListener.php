<?php

namespace App\Listeners\Mimic\PushNotifications;

use App\Events\Mimic\MimicUpvotedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

class SendMimicUpvotedNotificationListener
{

    /**
     * Handle the event.
     *
     * @param  MimicUpvotedEvent  $event
     * @return void
     */
    public function handle(MimicUpvotedEvent $event)
    {

        //Don't send notification if user is upvoting his own mimic
        if ($event->user->id === $event->model->user_id) {
            return;
        }

        $apiCallParams = $this->getApiCallParams($event);

        $data =
        [
            'badge' => 1,
            'sound' => 'default',
            'title' => trans('notifications.upvote_mimic_title'),
            'body' => trans('notifications.upvote_mimic_body', ['user' => $event->user->username]),
            'parameters' => [
                'api_call_params' => array_merge(['page' => 1,], $apiCallParams),
                'position' => Constants::POSITION_SPLIT_SCREEN
            ],

        ];

        SendPushNotification::sendNotification($event->model->user_id, $data);
    }

    /**
     * @param  MimicUpvotedEvent  $event
     * @return void
     */
    private function getApiCallParams(MimicUpvotedEvent $event)
    {
        if (array_key_exists('original_mimic_id', $event->data)) {
            return [
                'user_id' => $event->model->user_id,
                'original_mimic_id' => $event->model->id,
            ];
        } 

        return [
            'user_id' => $event->model->originalMimic->user_id,
            'original_mimic_id' => $event->model->original_mimic_id,
            'response_mimic_id' => $event->model->id,
        ];
    }
}
