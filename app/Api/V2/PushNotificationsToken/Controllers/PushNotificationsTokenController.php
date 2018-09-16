<?php

namespace App\Api\V2\PushNotificationsToken\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;

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
}
