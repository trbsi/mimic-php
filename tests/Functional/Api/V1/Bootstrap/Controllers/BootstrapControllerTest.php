<?php

namespace Tests\Functional\Api\V1\Bootstrap\Controllers;

use Hash;
use Tests\TestCase;

class BootstrapControllerTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();
    }

    //--------------------------------Push notifications--------------------------------
    public function testPushTokenNotSet()
    {
    	$data = [];

        $response = $this->doPost('save-push-token', $data, 'v1');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
	    	'success' => false
	    ])
        ->assertStatus(200);
    }

    public function testPushTokenSetButEmpty()
    {
    	$data = ['push_token' => ''];

        $response = $this->doPost('save-push-token', $data, 'v1');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
	    ])
        ->assertStatus(200);
    }

    public function testDeviceIdNotSetButPushTokenIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz'];

        $response = $this->doPost('save-push-token', $data, 'v1');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
	    ])
        ->assertStatus(200);
    }

    public function testDeviceNotSetButEverythingElseIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333'];

        $response = $this->doPost('save-push-token', $data, 'v1');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
	    ])
        ->assertStatus(200);
    }

    public function testSuccesSavePushToken()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333', 'device' => 'ios'];

        $response = $this->doPost('save-push-token', $data, 'v1');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
	    	'success' => true
	    ])
        ->assertStatus(200);
    }

}