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

    //--------------------------------Listings--------------------------------
    public function testListUsersFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=1', $data);
        $assertData = [
            'id' => 2,
            'email' => 'user2@mail.com',
            'username' => 'beachdude',
            'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'pivot_following' => 1,
            'pivot_followed_by' => 2,
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('followers'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'followers'))
        ->assertStatus(200);
    }

    public function testListIfUserHasNoFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=10', $data);

        $response
        ->assertJsonStructure([
            'followers'
        ])
        ->assertJson([
            'followers' => []
        ])
        ->assertStatus(200);
    }

    public function testListAllPeopleThatLoggedinUserIsFollowing()
    {
        $data = [];

        $response = $this->doGet('profile/following?user_id=1', $data);
        $assertData = [
            'id' => 2,
            'email' => 'user2@mail.com',
            'username' => 'beachdude',
            'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'pivot_followed_by' => 1,
            'pivot_following' => 2,
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('following'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'following'))
        ->assertStatus(200);
    }

    public function testListIfUserIsFollowingNoOne()
    {
        $data = [];

        $response = $this->doGet('profile/following?user_id=10', $data);

        $response
        ->assertJsonStructure([
            'following'
        ])
        ->assertJson([
            'following' => []
        ])
        ->assertStatus(200);
    }

    //--------------------------------Follow/unfollow--------------------------------
    public function testUserSuccessfullyFollowedAnotherUser()
    {
        $user = User::find(5);
        $user->update(['followers' => 1]);
        
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
        $user->update(['followers' => 2]);
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