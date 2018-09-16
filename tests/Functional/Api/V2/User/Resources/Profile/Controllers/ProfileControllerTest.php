<?php

namespace Tests\Functional\Api\V2\User\Resources\Profile\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use App\Api\V2\User\Models\User;
use App\Api\V2\Hashtag\Models\Hashtag;
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
            'profile' => [
                'bio' => "This is my bio, which is little bit too big. I even user emojis and #swag. 😀 😁 😂 \nI need to check it out! I Like #kissing and #dance",
                'hashtags' => [
                    [
                        'hashtag_id' => 4,
                        'hashtag_name' => '#kissing'
                    ],
                    [
                        'hashtag_id' => 8,
                        'hashtag_name' => '#dance'
                    ],
                    [
                        'hashtag_id' => 11,
                        'hashtag_name' => '#swag'
                    ],
                ],
            ],
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
    public function testBioUpdatedSuccessfullyWithoutHashtags()
    {
        $bio = '😁 Totally new BIO! 😀';
        $data = [
            'bio' => $bio
        ];

        $assertData = [
            'id' => 95,
            'email' => 'user95@mail.com',
            'username' => 'sanjas',
            'profile_picture' => 'http://mimic.loc/files/hr/female/95.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false,
            'is_blocked' => false,
            'profile' => [
                'bio' => $bio,
                'hashtags' => [],
            ],
        ];

        $response = $this->doPut('user/profile', $data);

        $response->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('profile'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'profile'))
        ->assertStatus(200);

        $user = User::find($this->loggedUserId);
        $this->assertEquals($bio, $user->profile->bio);
    }

    public function testBioUpdatedSuccessfullyWithNewHashtags()
    {
        $bio = '😁 Totally new BIO with #hashtag #totallynew! 😀';
        $data = [
            'bio' => $bio
        ];

        $assertData = [
            'id' => 95,
            'email' => 'user95@mail.com',
            'username' => 'sanjas',
            'profile_picture' => 'http://mimic.loc/files/hr/female/95.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false,
            'is_blocked' => false,
            'profile' => [
                'bio' => $bio,
                'hashtags' => [
                    [
                        'hashtag_name' => '#hashtag'
                    ],
                    [
                        'hashtag_name' => '#totallynew'
                    ],
                ],
            ],
        ];

        $response = $this->doPut('user/profile', $data);

        $response->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('profile'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'profile'))
        ->assertStatus(200);

        $user = User::find($this->loggedUserId);
        $this->assertEquals($bio, $user->profile->bio);
    }

    public function testBioUpdatedSuccessfullyWithExistingHashtags()
    {
        //get number of hashtags before updating profile
        $numberBeforeUpdate = Hashtag::count();

        $bio = '😁 Totally new BIO with #swag #yolo! 😀';
        $data = [
            'bio' => $bio
        ];

        $assertData = [
            'id' => 95,
            'email' => 'user95@mail.com',
            'username' => 'sanjas',
            'profile_picture' => 'http://mimic.loc/files/hr/female/95.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false,
            'is_blocked' => false,
            'profile' => [
                'bio' => $bio,
                'hashtags' => [
                    [
                        'hashtag_name' => '#swag'
                    ],
                    [
                        'hashtag_name' => '#yolo'
                    ],
                ],
            ],
        ];

        $response = $this->doPut('user/profile', $data);

        $response->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('profile'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'profile'))
        ->assertStatus(200);

        $numberAfterUpdate = Hashtag::count();
        $this->assertEquals($numberBeforeUpdate, $numberAfterUpdate);
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
