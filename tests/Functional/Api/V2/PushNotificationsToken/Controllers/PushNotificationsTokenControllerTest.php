<?php

namespace Tests\Functional\Api\V2\PushNotificationsToken\Controllers;

use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\PushNotificationsToken\Assert;

class PushNotificationsTokenControllerTest extends TestCaseV2
{
    public function setUp()
    {
        parent::setUp();
        $this->assert = $this->app->make(Assert::class);
    }

    public function tearDown()
    {
        $this->assert = null;
        parent::tearDown();
    }
    
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

    public function testRequiredDataNotSent()
    {
        $data = [];

        $response = $this->doPost('push-notifications-token/save-push-token', $data);

        $errorsStructure = [
            'device_id',
            'device',
            'push_token',
        ];
        
        $errors = [
            'device_id' => [
                'validation.required'
            ],
            'device' => [
                'validation.required'
            ],
            'push_token' => [
                'validation.required'
            ],
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testPushTokenIsNotInRange()
    {
        $data = [
            'device' => 'not_ios',
            'device_id' => 'xyz123abc',
            'push_token' => 'AAABBBCCC111222333',
        ];

        $response = $this->doPost('push-notifications-token/save-push-token', $data);

        $errorsStructure = [
            'device',
        ];
        
        $errors = [
            'device' => [
                'validation.in'
            ],
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testSuccesSavePushToken()
    {
        $data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333', 'device' => 'ios'];

        $response = $this->doPost('push-notifications-token/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess(['success' => true]))
        ->assertStatus(200);
    }
}
