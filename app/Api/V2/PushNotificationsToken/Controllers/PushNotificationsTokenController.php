<?php

namespace App\Api\V2\PushNotificationsToken\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Illuminate\Http\Request;
use App\Helpers\SendPushNotification;

class PushNotificationsTokenController extends BaseAuthController
{
    /**
     * Delete tokens of a logged in user
     *
     * @return Response
     */
    public function deleteByUser()
    {
        PushNotificationsToken::where('user_id', $this->authUser->id)->delete();
        return response()->json([], 204);
    }

    /**
     * Send notification to all users
     */
    public function sendNotificationToEveryone(Request $request)
    {
        if ($request->password !== 'mimic-push-everyone') {
            abort(400);
        }

        $data = $request->all(); 
        if ($request->url) {   
            $data['media-url'] = $request->url;
            $data['media-type'] = 'url';
        }

        SendPushNotification::sendNotificationToEveryone($data);
        return response()->json([], 204);
    }
}
