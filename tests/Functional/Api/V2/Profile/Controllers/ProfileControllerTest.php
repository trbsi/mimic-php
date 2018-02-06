<?php

namespace Tests\Functional\Api\V2\Profile\Controllers;

use Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();
    }

    //--------------------------------Get profile--------------------------------
    public function testGetUserProfileSuccessfully()
    {
    	$data = [];

        $response = $this->doGet('profile/user?id=1', $data, 'v2');

        $response
        ->assertJsonStructure([
            'id',
            'email',
            'username',
            'profile_picture',
            'followers',
            'following' ,
            'number_of_mimics',
            'created_at',
            'updated_at',
            'i_am_following_you'
        ])
        ->assertJson([
            'id' => 1,
            'email' => 'user1@mail.com',
            'username' => 'AndrewCG',
            'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false
	    ])
        ->assertStatus(200);
    }

    public function testUserNotFound()
    {
        $data = [];

        $response = $this->doGet('profile/user?id=200', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => "User not found"
            ]
        ])
        ->assertStatus(404);
    }

    //--------------------------------Block profile--------------------------------
    public function testSuccessfullyBlockUser()
    {
        $data = ['user_id' => 5];

        $response = $this->doPost('profile/block', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'blocked'
        ])
        ->assertStatus(200);
    }
}