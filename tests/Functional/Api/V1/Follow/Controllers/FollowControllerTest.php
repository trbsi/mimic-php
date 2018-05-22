<?php

namespace Tests\Functional\Api\V1\Follow\Controllers;

use Hash;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    //--------------------------------Listings--------------------------------
    public function testListUsersFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=1', $data, 'v1');

        $response
        ->assertJsonStructure([
            'followers' => [
                '*' => [
                    'id',
                    'email',
                    'username',
                    'profile_picture',
                    'followers',
                    'following',
                    'number_of_mimics',
                    'created_at',
                    'updated_at',
                    'pivot' => [
                        'following',
                        'followed_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ])
        ->assertJson([
            'followers' => [
                [
                    'id' => 2,
                    'email' => 'user2@mail.com',
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'pivot' => [
                        'following' => 1,
                        'followed_by' => 2,
                    ]
                ]
            ]
        ])
        ->assertStatus(200);
    }

    public function testListIfUserHasNoFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=10', $data, 'v1');

        $response
        ->assertJsonStructure([
            'followers' => []
        ])
        ->assertJson([
            'followers' => []
        ])
        ->assertStatus(200);
    }

    public function testListAllPeopleThatLoggedinUserIsFollowing()
    {
        $data = [];

        $response = $this->doGet('profile/following?user_id=1', $data, 'v1');

        $response
        ->assertJsonStructure([
            'following' => [
                [
                    'id',
                    'email',
                    'username',
                    'profile_picture',
                    'followers',
                    'following',
                    'number_of_mimics',
                    'created_at',
                    'updated_at',
                    'pivot' => [
                        'followed_by',
                        'following',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ])
        ->assertJson([
            'following' => [
                [
                    'id' => 2,
                    'email' => 'user2@mail.com',
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'pivot' => [
                        'followed_by' => 1,
                        'following' => 2,
                    ]
                ]
            ]
        ])
        ->assertStatus(200);
    }

    public function testListIfUserIsFollowingNoOne()
    {
        $data = [];

        $response = $this->doGet('profile/following?user_id=10', $data, 'v1');

        $response
        ->assertJsonStructure([
            'following' => []
        ])
        ->assertJson([
            'following' => []
        ])
        ->assertStatus(200);
    }

    //--------------------------------Follow/unfollow--------------------------------
    public function testUserSuccessfullyFollowedAnotherUser()
    {
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data, 'v1');

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'followed'
        ])
        ->assertStatus(200);
    }

    public function testUserSuccessfullyUnFollowedAnotherUser()
    {
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data, 'v1');

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'unfollowed'
        ])
        ->assertStatus(200);
    }

}