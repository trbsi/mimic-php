<?php

namespace App\Functional\Api\V1\Controllers;

use Hash;
use App\Models\CoreUser as User;
use App\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    //use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
    }

    public function testFacebookLoginSuccessfully()
    {

        $response = $this->json('POST', 'api/auth/login', [
            "provider" => "facebook",
            "provider_data" => [
                "birthday" => "12/29/1991",
                "email" => "dario_facebook@yahoo.com",
                "first_name" => "Dario",
                "gender" => "male",
                "id" => "2042074229356674",
                "last_name" => "Trbović",
                "picture" => [ 
                    "data" => [
                        "url" => "http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg"]
                    ]
              ]
        ],
        [
            'AllowEntry' => $this->allow_entry,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                    "username",
                    "token",
                    "user_id",
                    "email"
                ])
            ->assertStatus(200);
    }


    public function testTwitterLoginSuccessfully()
    {

        $response = $this->json('POST', 'api/auth/login', [
            "provider" => "twitter",
            "provider_data" => [
                "birthday" => "12/29/1991",
                "email" => "dario_twitter@yahoo.com",
                "first_name" => "Dario",
                "gender" => "male",
                "id" => "2042074229356674",
                "last_name" => "Trbović",
                "profile_image_url" =>  "http://pbs.twimg.com/profile_images/834863598199513088/53W0-JKZ_normal.jpg"
            ]
        ],
        [
            'AllowEntry' => $this->allow_entry,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                    "username",
                    "token",
                    "user_id",
                    "email"
                ])
            ->assertStatus(200);
    }

   /* public function testLoginWithReturnsWrongCredentialsError()
    {
        $this->post('api/auth/login', [
            'email' => 'unknown@email.com',
            'password' => '123456'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(403);
    }

    public function testLoginWithReturnsValidationError()
    {
        $this->post('api/auth/login', [
            'email' => 'test@email.com'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(422);
    }*/
}
