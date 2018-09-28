<?php

namespace App\Api\V2\PushNotificationsToken\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Illuminate\Http\Request;
use App\Helpers\SendPushNotification;
use App\Api\V2\PushNotificationsToken\Requests\SaveTokenRequest;

class PushNotificationsTokenController extends BaseAuthController
{
    /**
     * @param  SaveTokenRequest $request
     * @return Response
     */
    public function saveOrUpdateToken(SaveTokenRequest $request)
    {
        $PNT = PushNotificationsToken::where([
            'user_id' => $this->authUser->id,
            'device' => $request->device,
            'device_id' => $request->device_id
        ])
        ->first();

        //you cannot find anything in database, so save it
        if (empty($PNT)) {
            $PNT = new PushNotificationsToken;
            $PNT->user_id = $this->authUser->id;
            $PNT->device_id = $request->device_id;
            $PNT->token = $request->push_token;
            $PNT->device = strtolower($request->device);
            $PNT->save();
        } //there is something in database
        else {
            $PNT->token = $request->push_token;
            $PNT->update();
        }

        return response()->json(['success' => true]);
    }

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
