<?php

namespace Tests\Functional\Api\V2\User\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\User\Assert;
use App\Api\V2\User\Models\User;

class UserControllerTest extends TestCaseV2
{
	public function setUp()
    {
        parent::setUp();
        $this->assert = new Assert();
    }

    //--------------------------------Get profile--------------------------------
    public function testGetUserProfileSuccessfully()
    {
    	$data = [];

        $response = $this->doGet('profile/user?id=1', $data);
        $assertData = [
            'id' => 1,
            'email' => 'user1@mail.com',
            'username' => 'AndrewCG',
            'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false,
            'is_blocked' => false,
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('profile'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'profile'))
        ->assertStatus(200);
    }

    public function testUserNotFound()
    {
        $data = [];
        $response = $this->doGet('profile/user?id=20000', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError( trans('core.user.user_not_found')))
        ->assertStatus(404);
    }

    //--------------------------------Block profile--------------------------------
    public function testSuccessfullyBlockUser()
    {
        $data = ['user_id' => 5];
        $response = $this->doPost('profile/block', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'blocked'
        ])
        ->assertStatus(200);
    }

    public function testSuccessfullyUnBlockUser()
    {
        //block user first
        $user = User::find($this->loggedUserId);
        $user->blockedUsers()->attach(5);

        $data = ['user_id' => 5];
        $response = $this->doPost('profile/block', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'unblocked'
        ])
        ->assertStatus(200);
    }

    public function testCantBlockYourself()
    {
        $data = ['user_id' => $this->loggedUserId];

        $response = $this->doPost('profile/block', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('users.cant_block_yourself')))
        ->assertStatus(400);
    }
}