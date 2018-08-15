<?php

namespace Tests\Functional\Api\V2\Bootstrap\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\Bootstrap\Assert;

class BootstrapControllerTest extends TestCaseV2
{
	public function setUp()
    {
        parent::setUp();
        $this->assert = $this->app->make(Assert::class);
    }

    //--------------------------------Push notifications--------------------------------
    public function testPushTokenNotSet()
    {
    	$data = [];

        $response = $this->doPost('bootstrap/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('core.push_token.parameters_not_set')))
        ->assertStatus(400);
    }

    public function testPushTokenSetButEmpty()
    {
    	$data = ['push_token' => ''];

        $response = $this->doPost('bootstrap/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('core.push_token.parameters_not_set')))
        ->assertStatus(400);
    }

    public function testDeviceIdNotSetButPushTokenIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz'];

        $response = $this->doPost('bootstrap/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('core.push_token.parameters_not_set')))
        ->assertStatus(400);
    }

    public function testDeviceNotSetButEverythingElseIsSet()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333'];

        $response = $this->doPost('bootstrap/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('core.push_token.parameters_not_set')))
        ->assertStatus(400);
    }

    public function testSuccesSavePushToken()
    {
    	$data = ['push_token' => 'xxxyyyzzz', 'device_id' => '111222333', 'device' => 'ios'];

        $response = $this->doPost('bootstrap/save-push-token', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess(['success' => true]))
        ->assertStatus(200);
    }

}