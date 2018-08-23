<?php

namespace App\Listeners\Mimic\PushNotifications;

use App\Events\Mimic\MimicCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;

class SendMimicCreatedNotificationListener
{
    /**
     * Handle the event.
     *
     * @param  MimicCreatedEvent  $event
     * @return void
     */
    public function handle(MimicCreatedEvent $event)
    {
        //Don't send notification if this is not response or if user is responding to his own Mimic
        if (!$event->isResponseMimic 
            ||
            $event->isResponseMimic && $event->user->id === $event->model->originalMimic->user_id
        ) {
            return;
        }


        $data = [
            'badge' => 1,
            'sound' => 'default',
            'title' => trans('notifications.new_response_title'),
            'body' => trans('notifications.new_response_body', ['user' => $event->user->username]),
            'media-url' => $event->model->file_url,
            'media-type' => $event->model->mimic_type,
            'parameters' => [
                'api_call_params' => [
                    'page' => 1,
                    'user_id' => $event->model->originalMimic->user_id,
                    'original_mimic_id' => $event->model->original_mimic_id,
                    'response_mimic_id' => $event->model->id,
                ],
                'position' => Constants::POSITION_SPLIT_SCREEN,
            ],
        ];

        SendPushNotification::sendNotification($event->model->originalMimic->user_id, $data);        
    }
}
