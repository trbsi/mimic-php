<?php

namespace Tests\Functional\Api\V2\User\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\User\Assert;
use App\Api\V2\User\Models\User;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Models\MimicTaguser;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Tests\TestCaseHelper;
use Illuminate\Support\Facades\Storage;
use Tests\Functional\Api\V2\Mimic\Helpers\MimicTestHelper;
use App\Api\V2\User\Resources\Profile\Models\Profile;
use Tests\Functional\Api\V2\User\Resources\Profile\Assert as AssertProfile;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;

class UserControllerTest extends TestCaseV2
{
    /**
     * @var Assert
     */
    private $assert;

    /**
     * @var AssertProfile
     */
    private $assertProfile;

    public function setUp()
    {
        parent::setUp();
        $this->assert = $this->app->make(Assert::class);
        $this->assertProfile = $this->app->make(AssertProfile::class);
    }

    public function tearDown()
    {
        $this->assert = null;
        parent::tearDown();
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
        ->assertJson($this->assert->getAssertJsonOnError("You can't block yourself!"))
        ->assertStatus(400);
    }

    //UPDATE USER
    public function testSuccessfullyUpdateUser()
    {
        $username = 'AndrewCG1';
        $email = 'user1@mail1.com';
        $data = [
            'id' => $this->loggedUserId,
            'username' => $username,
            'email' => $email,
        ];

        $assertData = [
            'id' => 95,
            'email' => $email,
            'username' => $username,
            'profile_picture' => 'http://mimic.loc/files/hr/female/95.jpg',
            'followers' => '123M',
            'following' => '123M',
            'number_of_mimics' => '123M',
            'i_am_following_you' => false,
            'is_blocked' => false,
            'profile' => [
                'bio' => "This is my bio, which is little bit too big. I even use emojis and #swag. 😀 😁 😂 \nI need to check it out! I Like #kissing and #dance",
                'hashtags' => [
                    [
                        'hashtag_name' => '#kissing'
                    ],
                    [
                        'hashtag_name' => '#dance'
                    ],
                    [
                        'hashtag_name' => '#swag'
                    ],
                ],
            ],
        ];

        $response = $this->doPut('user', $data);
        $response->assertJsonStructure($this->assertProfile->getAssertJsonStructureOnSuccess('profile'))
        ->assertJson($this->assertProfile->getAssertJsonOnSuccess($assertData, 'profile'))
        ->assertStatus(200);

        $user = User::find($this->loggedUserId);
        $this->assertEquals($username, $user->username);
        $this->assertEquals($email, $user->email);
    }

    public function testUpdateUserWhenEmailAndUsernameAreNotValid()
    {
        $data = [
            'id' => $this->loggedUserId,
            'username' => 'abc%&/',
            'email' => 'user1mail1.com',
        ];

        $errorsStructure = [
            'email',
            'username'
        ];

        $errors = [
            'email' => [
                'Email is not in valid format.',
            ],
            'username' => [
                "Username can only contain letters, numbers, '.' and '-'. It should be min 4 characters long.",
            ],
        ];

        $response = $this->doPut('user', $data);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUpdateUserWhenEmailAndUsernameExist()
    {
        $data = [
            'id' => $this->loggedUserId,
            'username' => 'AndrewCG',
            'email' => 'user1@mail.com',
        ];

        $errorsStructure = [
            'email',
            'username'
        ];

        $errors = [
            'email' => [
                'This email already exists.',
            ],
            'username' => [
                'This username already exists, try another one.',
            ],
        ];

        $response = $this->doPut('user', $data);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    //DELETE USER
    public function testDeleteAccount()
    {
        $mimicId = 1;

        //insert followers
        Follow::create(['followed_by' => $this->loggedUserId, 'following' => 1]);
        Follow::create(['followed_by' => 1, 'following' => $this->loggedUserId]);

        //insert user tagging
        MimicTaguser::create(['mimic_id' => $mimicId, 'user_id' => $this->loggedUserId]);

        //insert blockings
        $user = User::find($this->loggedUserId);
        $user->blockedUsers()->attach(['user_id' => 1]);
        $user->blockedFrom()->attach(['blocked_by' => 1]);

        //insert push tokens
        for ($i=0; $i < 5 ; $i++) {
            PushNotificationsToken::create([
                'user_id' => $this->loggedUserId,
                'token' => md5(mt_rand()),
                'device' => 'ios',
                'device_id' => md5(mt_rand()),
            ]);
        }

        //create new mimic
        $path = public_path().'/files/user/1/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '1-1.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, '1-1.jpg', 'image/jpg');
        $data = [
            'description' => '#skate #backflip #frontflip',
            'mimic_file' => $file,
            'video_thumbnail' => $videoThumbnail,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];
        $response = $this->doPost('mimic/create', $data);
        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileNames[] = MimicTestHelper::getMimicFileName($responseArray);
        $fileNames[] = MimicTestHelper::getMimicVideoThumbnailName($responseArray);

        //create response
        $path = public_path().'/files/user/1/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '1-1.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, '1-1.jpg', 'image/jpg');
        $data = [
            'mimic_file' => $file,
            'original_mimic_id' => $mimicId,
            'video_thumbnail' => $videoThumbnail,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);
        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileNames[] = MimicTestHelper::getMimicFileName($responseArray);
        $fileNames[] = MimicTestHelper::getMimicVideoThumbnailName($responseArray);

        //delete user
        $response = $this->doDelete('user', []);
        $response->assertStatus(204);

        //assert profile
        $this->assertTrue(empty(Profile::where('user_id', $this->loggedUserId)->first()));

        //assert follow
        $this->assertTrue(Follow::where('followed_by', $this->loggedUserId)->get()->isEmpty());
        $this->assertTrue(Follow::where('following', $this->loggedUserId)->get()->isEmpty());

        //assert user tagging
        $this->assertTrue(MimicTaguser::where('user_id', $this->loggedUserId)->get()->isEmpty());

        //assert blockings
        $this->assertTrue($user->blockedUsers()->get()->isEmpty());
        $this->assertTrue($user->blockedFrom()->get()->isEmpty());

        //assert push tokens
        $this->assertTrue(PushNotificationsToken::where('user_id', $this->loggedUserId)->get()->isEmpty());

        //assert files
        foreach ($fileNames as $fileName) {
            $path = sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $fileName);
            Storage::disk('public')->assertMissing($path);
        }
    }
}
