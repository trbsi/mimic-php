<?php

namespace Tests\Functional\Api\V2\Auth\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\Auth\Assert;
use Tests\TestCaseHelper;
use App\Api\V2\User\Models\User;

class LoginControllerTest extends TestCaseV2
{
    /**
     * @var Assert
     */
    private $assert;

    
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

    //--------------------------------Facebook--------------------------------
    public function testFacebookLoginSuccessfullyWithEmailIncluded()
    {
        $data = [
            'provider' => 'facebook',
            'provider_data' => [
                'birthday' => '12/29/1991',
                'email' => 'dario_facebook@yahoo.com',
                'first_name' => 'Dario',
                'gender' => 'male',
                'id' => '2042074229356674',
                'last_name' => 'Trbović',
                'picture' => [
                    'data' => [
                        'url' => 'http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg']
                    ]
              ]
        ];

        $response = $this->doPost('auth/login', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess([
            'username' => null,
            'email' => 'dario_facebook@yahoo.com',
        ]))
        ->assertStatus(200);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $user = User::find($responseArray['user_id']);
        $profileCount = $user->profile()->count();

        $this->assertEquals(1, $profileCount);
        $this->assertEquals('https://graph.facebook.com/2042074229356674/picture?type=large', $user->profile_picture);
        $this->assertEquals('dario_facebook@yahoo.com', $user->email);
    }

    public function testFacebookLoginSuccessfullyWithoutEmailIncluded()
    {
        $data = [
            'provider' => 'facebook',
            'provider_data' => [
                'birthday' => '12/29/1991',
                'first_name' => 'Dario',
                'gender' => 'male',
                'id' => '111000111',
                'last_name' => 'Trbović',
                'picture' => [
                    'data' => [
                        'url' => 'http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg']
                    ]
              ]
        ];

        $response = $this->doPost('auth/login', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess([
            'username' => null,
            'email' => null,
        ]))
        ->assertStatus(200);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $user = User::find($responseArray['user_id']);
        $profileCount = $user->profile()->count();

        $this->assertEquals(1, $profileCount);
        $this->assertEquals('https://graph.facebook.com/111000111/picture?type=large', $user->profile_picture);
        $this->assertEquals(null, $user->email);
    }

    //--------------------------------Twitter--------------------------------
    public function testTwitterLoginSuccessfullyWithEmailIncluded()
    {
        $data = [
            'provider' => 'twitter',
            'provider_data' => [
                'birthday' => '12/29/1991',
                'email' => 'dario_twitter@yahoo.com',
                'first_name' => 'Dario',
                'gender' => 'male',
                'id' => '2042074229356674',
                'last_name' => 'Trbović',
                'profile_image_url' =>  'http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg'
            ]
        ];

        $response = $this->doPost('auth/login', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess([
            'username' => null,
            'email' => 'dario_twitter@yahoo.com',
        ]))
        ->assertStatus(200);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $user = User::find($responseArray['user_id']);
        $profileCount = $user->profile()->count();

        $this->assertEquals(1, $profileCount);
        $this->assertEquals('http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg', $user->profile_picture);
        $this->assertEquals('dario_twitter@yahoo.com', $user->email);
    }

    public function testTwitterLoginSuccessfullyWithoutEmailIncluded()
    {
        $data = [
            'provider' => 'twitter',
            'provider_data' => [
                'birthday' => '12/29/1991',
                'first_name' => 'Dario',
                'gender' => 'male',
                'id' => '2042074229356674',
                'last_name' => 'Trbović',
                'profile_image_url' =>  'http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg'
            ]
        ];

        $response = $this->doPost('auth/login', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess())
        ->assertJson($this->assert->getAssertJsonOnSuccess([
            'username' => null,
            'email' => null,
        ]))
        ->assertStatus(200);


        $responseArray = TestCaseHelper::decodeResponse($response);
        $user = User::find($responseArray['user_id']);
        $profileCount = $user->profile()->count();

        $this->assertEquals(1, $profileCount);
        $this->assertEquals('http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg', $user->profile_picture);
        $this->assertEquals(null, $user->email);
    }


    //--------------------------------Username set--------------------------------
    public function testSetUsernameWhereUsernameEmpty()
    {
        $data = [
            'username' => '',
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError('Username cannot be empty.'))
        ->assertStatus(403);
    }

    public function testUsernameMinFourLetters()
    {
        $data = [
            'username' => 'xyz',
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError("Username can only contain letters, numbers, '.' and '-'. It should be min 4 characters long."))
        ->assertStatus(403);
    }

    public function testUsernameOnlyNumbersAndLetters()
    {
        $data = [
            'username' => 'xyz123&',
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError("Username can only contain letters, numbers, '.' and '-'. It should be min 4 characters long."))
        ->assertStatus(403);
    }

    public function testUsernameExists()
    {
        $data = [
            'username' => 'AndrewCG',
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError('This username already exists, try another one.'))
        ->assertStatus(403);
    }


    //--------------------------------Email set--------------------------------
    public function testEmailExists()
    {
        $data = [
            'username' => 'xyz123',
            'email' => 'user1@mail.com'
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError('This email already exists.'))
        ->assertStatus(403);
    }

    public function testEmailisNotInValidFormat()
    {
        $data = [
            'username' => 'xyz123',
            'email' => 'user1mail.com'
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError('Email is not in valid format.'))
        ->assertStatus(403);
    }

    //--------------------------------Success save username/password--------------------------------
    public function testSuccessSaveUsername()
    {
        $data = [
            'username' => 'xyz123',
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure([
            'status'
        ])
        ->assertJson([
            'status' => true
        ])
        ->assertStatus(200);

        $user = User::find($this->loggedUserId);
        $this->assertEquals('xyz123', $user->username);
    }


    public function testSuccessSaveUsernameAndEmail()
    {
        $data = [
            'username' => 'xyz1234',
            'email' => 'unknow@mail.com'
        ];

        $response = $this->doPost('set-username', $data);

        $response
        ->assertJsonStructure([
            'status'
        ])
        ->assertJson([
            'status' => true
        ])
        ->assertStatus(200);

        $user = User::find($this->loggedUserId);
        $this->assertEquals('xyz1234', $user->username);
        $this->assertEquals('unknow@mail.com', $user->email);
    }
}
