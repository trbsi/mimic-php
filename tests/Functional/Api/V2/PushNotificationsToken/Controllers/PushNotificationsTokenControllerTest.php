<?php

namespace Tests\Functional\Api\V2\PushNotificationsToken\Controllers;

use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Tests\Functional\Api\V2\TestCaseV2;

class PushNotificationsTokenControllerTest extends TestCaseV2
{
    public function testDeleteByUser()
    {
    	for ($i=0; $i < 5; $i++) { 
    		PushNotificationsToken::create([
    			'user_id' => $this->loggedUserId, 
    			'token' => md5(mt_rand()), 
    			'device' => 'ios', 
    			'device_id' => md5(mt_rand()),
    		]);
    	}
    	$result = PushNotificationsToken::where('user_id', $this->loggedUserId)->get();
    	$this->assertFalse($result->isEmpty());

    	$response = $this->doDelete('push-notifications-token/delete-by-user', []);
    	$response->assertStatus(204);

    	$result = PushNotificationsToken::where('user_id', $this->loggedUserId)->get();
    	$this->assertTrue($result->isEmpty());
    }
}
