<?php

namespace Tests\Functional\Api\V2\User\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\User\Assert;
use App\Api\V2\User\Models\User;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Models\MimicResponseUpvote;
use App\Api\V2\Mimic\Models\MimicUpvote;
use App\Api\V2\Mimic\Models\MimicTaguser;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Tests\TestCaseHelper;
use Illuminate\Support\Facades\Storage;
use Tests\Functional\Api\V2\Mimic\Helpers\MimicTestHelper;
use App\Api\V2\User\Resources\Profile\Models\Profile;

class UserControllerTest extends TestCaseV2
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

    //UPDATE PROFILE
    public function testSuccessfullyUpdateUser()
    {
        $username = 'AndrewCG1';
        $email = 'user1@mail1.com';
        $data = [
            'id' => $this->loggedUserId,
            'username' => $username,
            'email' => $email,
        ];

        $response = $this->doPut('user', $data);
        $response->assertStatus(204);

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
        //insert followers
        Follow::create(['followed_by' => $this->loggedUserId, 'following' => 1]);
        Follow::create(['followed_by' => 1, 'following' => $this->loggedUserId]);

        //insert upvotes
        MimicUpvote::create(['mimic_id' => 1, 'user_id' => $this->loggedUserId]);
        MimicResponseUpvote::create(['mimic_id' => 1, 'user_id' => $this->loggedUserId]);

        //insert user tagging
        MimicTaguser::create(['mimic_id' => 1, 'user_id' => $this->loggedUserId]);

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
            'hashtags' => '#skate #backflip #frontflip',
            'mimic_file' => $file,
            'video_thumbnail' => $videoThumbnail,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
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
            'original_mimic_id' => 1,
            'video_thumbnail' => $videoThumbnail,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
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

        //assert upvotes
        $this->assertTrue(MimicUpvote::where('user_id', $this->loggedUserId)->get()->isEmpty());
        $this->assertTrue(MimicResponseUpvote::where('user_id', $this->loggedUserId)->get()->isEmpty());

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
