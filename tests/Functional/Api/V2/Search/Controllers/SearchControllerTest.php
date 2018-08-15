<?php

namespace Tests\Functional\Api\V2\Search\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;

class SearchControllerTest extends TestCaseV2
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
                'popularity' => "123,456,789",
            ],
            [
                'id' => 2,
                'name' => '#jump',
                'popularity' => "123,456,789",
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
                'updated_at',
                'i_am_following_you',
                'is_blocked',
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
                'i_am_following_you' => false,
                'is_blocked' => false,
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

    public function testGetTopTenHashtagsAndUsers()
    {
        $data = [];

        $response = $this->doGet('search/top', $data);

        $response
        ->assertJsonStructure([
            'hashtags' => [
                '*' => [
                    'id',
                    'name',
                    'popularity',
                ],
            ],
            'users' => [
                '*' => [
                    'id' ,
                    'email',
                    'username',
                    'profile_picture',
                    'followers',
                    'following',
                    'number_of_mimics',
                    'i_am_following_you',
                    'is_blocked',
                ],
            ]
        ])
        ->assertJson(
        [
            'hashtags' => [
                [
                    'id' => 11,
                    'name' => '#swag',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 10,
                    'name' => '#yolo',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 9,
                    'name' => '#swim',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 8,
                    'name' => '#dance',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 7,
                    'name' => '#meandmycrew',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 6,
                    'name' => '#playingsport',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 5,
                    'name' => '#comewithme',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 4,
                    'name' => '#kissing',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 3,
                    'name' => '#playingaround',
                    'popularity' => '123,456,789',
                ],
                [
                    'id' => 2,
                    'name' => '#jump',
                    'popularity' => '123,456,789',
                ]
            ],
            'users' => [
                [
                    'id' => 1,
                    'email' => 'user1@mail.com',
                    'username' => 'AndrewCG',
                    'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 2,
                    'email' => 'user2@mail.com',
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 3,
                    'email' => 'user3@mail.com',
                    'username' => 'Chrisburke04',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/3.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 4,
                    'email' => 'user4@mail.com',
                    'username' => 'Cognizant',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/4.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 5,
                    'email' => 'user5@mail.com',
                    'username' => 'Datguy1',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/5.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 6,
                    'email' => 'user6@mail.com',
                    'username' => 'deeptt_tacos',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/6.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 7,
                    'email' => 'user7@mail.com',
                    'username' => 'DerpyGirl',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/7.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 8,
                    'email' => 'user8@mail.com',
                    'username' => 'Desdemona',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/8.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 9,
                    'email' => 'user9@mail.com',
                    'username' => 'Desynchronized',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/9.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false,
                ],
                [
                    'id' => 10,
                    'email' => 'user10@mail.com',
                    'username' => 'DriveAlive',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/10.jpg',
                    'followers' => '123M',
                    'following' => '123M',
                    'number_of_mimics' => '123M',
                    'i_am_following_you' => false,
                    'is_blocked' => false
                ]

            ]
        ])
        ->assertStatus(200);
    }
}