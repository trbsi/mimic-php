<?php

namespace App\Functional\Api\V1\Controllers;

use Hash;
use App\TestCase;

class SearchControllerTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();
    }

    public function testSearchByHashtag()
    {
    	$data = [];

        $response = $this->doGet('search?term=%23jump', $data);

        $response
        ->assertJsonStructure([
            '*' => [
            	'id',
		        'name',
		        'popularity',
		        'created_at',
		        'updated_at',
		    ]
        ])
        ->assertJson([
            [
                'id' => 1,
                'name' => '#jumping',
                'popularity' => 123456789,
            ],
            [
                'id' => 2,
                'name' => '#jump',
                'popularity' => 123456789,
            ]
	    ])
        ->assertStatus(200);
    }

    public function testSearchByHashtagWithoutResults()
    {
    	$data = [];

        $response = $this->doGet('search?term=%23totallyfake', $data);

        $response
        ->assertJsonStructure([])
        ->assertJson([])
        ->assertStatus(200);
    }

    public function testSearchByUsername()
    {
    	$data = [];

        $response = $this->doGet('search?term=@andr', $data);

        $response
        ->assertJsonStructure([
            '*' => [
		        'id',
		        'email',
		        'username',
		        'profile_picture',
		        'followers',
		        'following',
		        'number_of_mimics',
		        'created_at',
		        'updated_at'
		    ]
        ])
        ->assertJson([
		    [
                'id' => 1,
                'email' => 'user1@mail.com',
                'username' => 'AndrewCG',
                'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
                'followers' => '123M',
                'following' => '123M',
                'number_of_mimics' => '123M',
		    ]
	    ])
        ->assertStatus(200);
    }

    public function testSearchByUsernameWithoutResults()
    {
    	$data = [];

        $response = $this->doGet('search?term=@totallyfake', $data);

        $response
        ->assertJsonStructure([])
        ->assertJson([])
        ->assertStatus(200);
    }

    public function testAtOrHashtagNotSetOnParameterTerm()
    {
    	$data = [];

        $response = $this->doGet('search?term=totallyfake', $data);

        $response
        ->assertJsonStructure([])
        ->assertJson([])
        ->assertStatus(200);
    }


}