<?php

namespace Tests\Functional\Api\V2\Follow\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\Follow\Assert;
use App\Api\V2\User\Models\User;
use App\Api\V2\Follow\Models\Follow;

class FollowControllerTest extends TestCaseV2
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

    //--------------------------------Listings--------------------------------
    public function testListUsersFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=1&page=1', $data);
        $assertData = [
            'pagination' => [
                'current_page' => 1,
                'first_page_url' => 'http://mimic.loc/api/profile/followers?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://mimic.loc/api/profile/followers?page=1',
                'next_page_url' => null,
                'path' => 'http://mimic.loc/api/profile/followers',
                'per_page' => 30,
                'prev_page_url' => null,
                'to' => 3,
                'total' => 3
            ],
            'followers' => [
                [
                    'id' => 12,
                    'username' => 'hogwartsthestral',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/12.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ],
                [
                    'id' => 11,
                    'username' => 'EmeraldDream',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/11.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ],
                [
                    'id' => 10,
                    'username' => 'DriveAlive',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/10.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ]
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followers'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followers'))
        ->assertStatus(200);
    }

    public function testListIfUserHasNoFollowers()
    {
        $data = [];

        $assertData = [
            'pagination' => [
                'current_page' => 1,
                'first_page_url' => 'http://mimic.loc/api/profile/followers?page=1',
                'from' => null,
                'last_page' => 1,
                'last_page_url' => 'http://mimic.loc/api/profile/followers?page=1',
                'next_page_url' => null,
                'path' => 'http://mimic.loc/api/profile/followers',
                'per_page' => 30,
                'prev_page_url' => null,
                'to' => null,
                'total' => 0
            ],
            'followers' => []
        ];

        $response = $this->doGet('profile/followers?user_id=90', $data);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followers'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followers'))
        ->assertStatus(200);
    }

    public function testListAllPeopleThatLoggedinUserIsFollowing()
    {
        $data = [];

        $response = $this->doGet('profile/following?user_id=1&page=1', $data);
        $assertData = [
            'pagination' => [
                'current_page' => 1,
                'first_page_url' => 'http://mimic.loc/api/profile/following?page=1',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => 'http://mimic.loc/api/profile/following?page=1',
                'next_page_url' => null,
                'path' => 'http://mimic.loc/api/profile/following',
                'per_page' => 30,
                'prev_page_url' => null,
                'to' => 3,
                'total' => 3
            ],
            'followings' => [
                [
                    'id' => 4,
                    'username' => 'Cognizant',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/4.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ],
                [
                    'id' => 3,
                    'username' => 'Chrisburke04',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/3.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ],
                [
                    'id' => 2,
                    'username' => 'beachdude',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'followers' => '123M',
                    'number_of_mimics' => '123M'
                ]
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followings'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followings'))
        ->assertStatus(200);
    }

    public function testListIfUserIsFollowingNoOne()
    {
        $data = [];

        $assertData = [
            'pagination' => [
                'current_page' => 1,
                'first_page_url' => 'http://mimic.loc/api/profile/following?page=1',
                'from' => null,
                'last_page' => 1,
                'last_page_url' => 'http://mimic.loc/api/profile/following?page=1',
                'next_page_url' => null,
                'path' => 'http://mimic.loc/api/profile/following',
                'per_page' => 30,
                'prev_page_url' => null,
                'to' => null,
                'total' => 0
            ],
            'followings' => []
        ];

        $response = $this->doGet('profile/following?user_id=90', $data);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followings'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followings'))
        ->assertStatus(200);
    }

    //--------------------------------Follow/unfollow--------------------------------
    public function testUserSuccessfullyFollowedAnotherUser()
    {
        $user = User::find(5);
        $user->followers = 1;
        $user->save();
        
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data);
        $assertData = [
            'type' => 'followed',
            'followers' => '2',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followed'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followed'))
        ->assertStatus(200);
    }

    public function testUserSuccessfullyUnFollowedAnotherUser()
    {
        //first follow user
        Follow::create([
            'followed_by' => $this->loggedUserId,
            'following' => 5
        ]);
        
        $user = User::find(5);
        $user->followers = 2;
        $user->save();
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data);
        $assertData = [
            'type' => 'unfollowed',
            'followers' => '1',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('unfollowed'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'unfollowed'))
        ->assertStatus(200);
    }
}
