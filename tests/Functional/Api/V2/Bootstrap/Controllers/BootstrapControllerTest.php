<?php

namespace Tests\Functional\Api\V2\Bootstrap\Controllers;

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

        $response = $this->doPost('bootstrap/save-push-token', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
	    	'error' => [
	    		'message' => trans('core.push_token.parameters_not_set')
	    	]
	    ])
        ->assertStatus(400);
    }

    public function testPushTokenSetButEmpty()
    {
    	$data = ['push_token' => ''];

        $response = $this->doPost('bootstrap/save-push-token', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
	    	'error' => [
	    		'message' => trans('core.push_token.parameters_not_set')
	    	]
	    ])
        ->assertStatus(400);
    }

    public function testDeviceIdNotSetButPushTokenIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz'];

        $response = $this->doPost('bootstrap/save-push-token', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
	    	'error' => [
	    		'message' => trans('core.push_token.parameters_not_set')
	    	]
	    ])
        ->assertStatus(400);
    }

    public function testDeviceNotSetButEverythingElseIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333'];

        $response = $this->doPost('bootstrap/save-push-token', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
	    	'error' => [
	    		'message' => trans('core.push_token.parameters_not_set')
	    	]
	    ])
        ->assertStatus(400);
    }

    public function testSuccesSavePushToken()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333', 'device' => 'ios'];

        $response = $this->doPost('bootstrap/save-push-token', $data, 'v2');

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