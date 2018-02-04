<?php

namespace Tests\Functional\Api\V2\Auth\Controllers;

use Hash;
use App\Models\CoreUser as User;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    //--------------------------------Facebook--------------------------------
    public function testFacebookFirstLoginSuccessfullyWithEmailIncluded()
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

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
	    	'username' => null,
	        'email' => 'dario_facebook@yahoo.com',
	    ])
        ->assertStatus(200);
    }

    public function testFacebookSecondLoginSuccessfullyWithoutEmailIncluded()
    {
    	$data = [
            'provider' => 'facebook',
            'provider_data' => [
                'birthday' => '12/29/1991',
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

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
	    	'username' => null,
	        'email' => 'dario_facebook@yahoo.com',
	    ])
        ->assertStatus(200);
    }

    public function testFacebookFirstLoginSuccessfullyWithoutEmailIncluded()
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

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
	    	'username' => null,
	        'email' => null,
	    ])
        ->assertStatus(200);
    }

    //--------------------------------Twitter--------------------------------
    public function testTwitterFirstLoginSuccessfullyWithEmailIncluded()
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

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
        	'username' => null,
            'email' => 'dario_twitter@yahoo.com',
        ])
        ->assertStatus(200);
    }

    public function testTwitterSecondLoginSuccessfullyWithoutEmailIncluded()
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

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
        	'username' => null,
            'email' => 'dario_twitter@yahoo.com',
        ])
        ->assertStatus(200);
    }

    public function testTwitterLoginSuccessfullyWithoutEmailIncluded()
    {

    	$data = [
            'provider' => 'twitter',
            'provider_data' => [
                'birthday' => '12/29/1991',
                'first_name' => 'Dario',
                'gender' => 'male',
                'id' => '000111000',
                'last_name' => 'Trbović',
                'profile_image_url' =>  'http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg'
            ]
        ];

        $response = $this->doPost('auth/login', $data, 'v2');

        $response
        ->assertJsonStructure([
                'username',
                'token',
                'user_id',
                'email'
            ])
        ->assertJson([
        	'username' => null,
            'email' => null,
        ])
        ->assertStatus(200);
    }

    //--------------------------------Username set--------------------------------
    public function testSetUsernameWhereUsernameEmpty()
    {
    	$data = [
            'username' => '',
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => trans('core.login.username_empty')
            ]
        ])
        ->assertStatus(403);
    }

    public function testUsernameMinFourLetters()
    {
    	$data = [
            'username' => 'xyz',
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => trans('core.login.username_contain')
            ]
        ])
        ->assertStatus(403);
    }

    public function testUsernameOnlyNumbersAndLetters()
    {
    	$data = [
            'username' => 'xyz123&',
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => trans('core.login.username_contain')
            ]
        ])
        ->assertStatus(403);
    }

    public function testUsernameExists()
    {
    	$data = [
            'username' => 'AndrewCG',
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => trans('core.login.username_exists')
            ]
        ])
        ->assertStatus(403);
    }


    //--------------------------------Email set--------------------------------
    public function testEmailExists()
    {
    	$data = [
            'username' => 'xyz123',
            'email' => 'user1@mail.com'
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => trans('core.login.email_exists')
            ]
        ])
        ->assertStatus(403);
    }

    //--------------------------------Success save username/password--------------------------------
    public function testSuccessSaveUsername()
    {
    	$data = [
            'username' => 'xyz123',
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'status'
        ])
        ->assertJson([
            'status' => true
        ])
        ->assertStatus(200);
    }


    public function testSuccessSaveUsernameAndEmail()
    {
    	$data = [
            'username' => 'xyz1234',
            'email' => 'unknow@mail.com'
        ];

        $response = $this->doPost('set-username', $data, 'v2');

        $response 
        ->assertJsonStructure([
            'status'
        ])
        ->assertJson([
            'status' => true
        ])
        ->assertStatus(200);
    }
}

