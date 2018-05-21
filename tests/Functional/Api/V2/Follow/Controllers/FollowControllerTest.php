<?php

namespace Tests\Functional\Api\V2\Follow\Controllers;

use Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Api\V2\User\Models\User;

class FollowControllerTest extends TestCase
{
    //--------------------------------Listings--------------------------------
    public function testListUsersFollowers()
    {
        $data = [];

        $response = $this->doGet('profile/followers?user_id=1', $data, 'v2');

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

        $response = $this->doGet('profile/followers?user_id=10', $data, 'v2');

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

        $response = $this->doGet('profile/following?user_id=1', $data, 'v2');

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

        $response = $this->doGet('profile/following?user_id=10', $data, 'v2');

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
        $user = User::find(5);
        $user->update(['followers' => 1]);
        
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'followers',
        ])
        ->assertJson([
            'type' => 'followed',
            'followers' => '2',
        ])
        ->assertStatus(200);
    }

    public function testUserSuccessfullyUnFollowedAnotherUser()
    {        
        $data = ['id' => 5];

        $response = $this->doPost('profile/follow', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'followers',
        ])
        ->assertJson([
            'type' => 'unfollowed',
            'followers' => '1',
        ])
        ->assertStatus(200);

        $user = User::find(5);
        $user->update(['followers' => 123456789]);
    }

}