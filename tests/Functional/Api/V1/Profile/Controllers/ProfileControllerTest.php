<?php

namespace App\Functional\Api\V1\Controllers;

use Hash;
use App\TestCase;

class ProfileControllerTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();
    }

    public function testGetUserProfileSuccessfully()
    {
    	$data = [];

        $response = $this->doGet('profile/user?id=1', $data);

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
}