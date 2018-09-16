<?php

namespace Tests\Functional\Api\V2\User\Resources\Profile\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use App\Api\V2\User\Models\User;
use Tests\Functional\Api\V2\User\Resources\Profile\Assert;

class ProfileControllerTest extends TestCaseV2
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
            'is_blocked' => false,
            'profile' => [
                'bio' => "This is my bio, which is little bit too big. I even user emojis and #hastags. 😀 😁 😂 \nI need to check it out!"
            ]
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
        ->assertJson($this->assert->getAssertJsonOnError('User not found'))
        ->assertStatus(404);
    }

    //UPDATE PROFILE
    public function testProfileSuccessfullyUpdated()
    {
        $bio = '😁 Totally new BIO! 😀';
        $data = [
            'bio' => $bio
        ];

        $response = $this->doPut('user/profile', $data);
        $response->assertStatus(204);

        $user = User::find($this->loggedUserId);
        $this->assertEquals($bio, $user->profile->bio);
    }

    public function testBioIsNotString()
    {
        $data = [
            'bio' => 123
        ];

        $errorsStructure = [
            'bio'
        ];

        $errors = [
            'bio' => [
                'Bio should be text'
            ]
        ];

        $response = $this->doPut('user/profile', $data);
        $response
            ->assertStatus(422)
            ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
            ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors));
    }

    public function testBioIsTooLong()
    {
        $data = [
            'bio' => str_repeat("This is test text. ", 1000),
        ];

        $errorsStructure = [
            'bio'
        ];

        $errors = [
            'bio' => [
                'Bio can be 1000 characters long'
            ]
        ];

        $response = $this->doPut('user/profile', $data);
        $response
            ->assertStatus(422)
            ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
            ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors));
    }
}
