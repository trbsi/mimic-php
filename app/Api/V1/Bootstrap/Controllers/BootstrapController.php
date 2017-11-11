<?php

namespace App\Api\V1\Bootstrap\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\User\Models\User;
use App\Api\V1\PushNotificationsToken\Models\PushNotificationsToken;

class BootstrapController extends Controller
{
    public function __construct(User $user, PushNotificationsToken $PNT)
    {
        $this->user = $user;
        $this->PNT = $PNT;
    }


    /**
     * @param Request $request
     */
    public function updateNotificationToken(Request $request)
    {
        if (isset($request->push_token) && !empty($request->push_token) && isset($request->device_id)) {

            $user = $this->user->getAuthenticatedUser();

            $PNT = PushNotificationsToken::where(['user_id' => $user->id, 'device' => $request->device, 'device_id' => $request->device_id])
                ->first();

            //you cannot find anything in database, so save it
            if (empty($PNT)) {
                $PNT = new PushNotificationsToken;
                $PNT->user_id = $user->id;
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

        return response()->json(['success' => false]);

    }

    /**
     * Used bymobile phone to check internet connection, this always returns success
     */
    public function heartbeat()
    {
        return response()->json(['success' => true]);
    }
}
