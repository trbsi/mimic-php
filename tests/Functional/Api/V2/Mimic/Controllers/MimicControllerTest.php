<?php

namespace Tests\Functional\Api\V2\Mimic\Controllers;

use Tests\TestCase;
use Tests\TestCaseHelper;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Models\MimicResponse;
use JWTAuth;
use Illuminate\Support\Facades\Storage;

class MimicControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    //--------------------------------Get mimics from a specific user--------------------------------
    public function testListMimicsFromUser()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=1', $data, 'v2');

        $response
        ->assertJsonStructure([
                'mimics' => [
                    '*' => [
                        'id',
                        'user_id',
                        'file',
                        'aws_file',
                        'video_thumb',
                        'aws_video_thumb',
                        'mimic_type',
                        'is_private',
                        'upvote',
                        'deleted_at',
                        'created_at',
                        'updated_at',
                        'file_url',
                        'video_thumb_url',
                    ]
                ]
            ])
        ->assertJson([
            'mimics' => [
                     [
                        'id' => 1,
                        'user_id' => 1,
                        'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'aws_file' => null,
                        'video_thumb' => 'bb4c28e90898e04516c86d398e165dee.jpg',
                        'aws_video_thumb' => null,
                        'mimic_type' => 'video',
                        'is_private' => false,
                        'upvote' => '123M',
                        'deleted_at' => null,
                        'created_at' => '1970-01-01 12:00:00',
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg',
                    ]
                ]
        ])
        ->assertStatus(200);
        
    }

    public function testListMimicsFromUserThatDoesntHaveMimics()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=10', $data, 'v2');

        $response
        ->assertJsonStructure([
                'mimics'
            ])
        ->assertJson([
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    public function testListResponseMimicsFromUser()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=2&get_responses=true', $data, 'v2');

        $response
        ->assertJsonStructure([
            'mimics' => [
                '*' => [
                    'id',
                    'user_id',
                    'original_mimic_id',
                    'file',
                    'aws_file',
                    'video_thumb',
                    'aws_video_thumb',
                    'mimic_type',
                    'upvote',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'file_url',
                    'video_thumb_url',
                    'original_mimic' => [
                        'id',
                        'user_id',
                        'file',
                        'aws_file',
                        'video_thumb',
                        'aws_video_thumb',
                        'mimic_type',
                        'is_private',
                        'upvote',
                        'deleted_at',
                        'created_at',
                        'updated_at',
                        'file_url',
                        'video_thumb_url',
                    ]
                ]
            ]
        ])
        ->assertJson([
            'mimics' => [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'original_mimic_id' => 1,
                    'file' => '46bcd7f8cc3373caea6b0efd888a5c2d.jpg',
                    'aws_file' => null,
                    'video_thumb' => null,
                    'aws_video_thumb' => null,
                    'mimic_type' => 'picture',
                    'upvote' => '123M',
                    'deleted_at' => null,
                    'created_at' => '1970-01-01 12:00:00',
                    'file_url' => 'http://mimic.loc/files/user/2/1970/01/46bcd7f8cc3373caea6b0efd888a5c2d.jpg',
                    'video_thumb_url' => null,
                    'original_mimic' => [
                        'id' => 1,
                        'user_id' => 1,
                        'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'aws_file' => null,
                        'video_thumb' => 'bb4c28e90898e04516c86d398e165dee.jpg',
                        'aws_video_thumb' => null,
                        'mimic_type' => 'video',
                        'is_private' => false,
                        'upvote' => '123M',
                        'deleted_at' => null,
                        'created_at' => '1970-01-01 12:00:00',
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg',
                    ]
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testListResponseMimicsFromUserThatDoesntHaveMimics()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=30', $data, 'v2');

        $response
        ->assertJsonStructure([
                'mimics'
            ])
        ->assertJson([
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    //--------------------------------Upvote/downvote--------------------------------
    public function testUpvoteOriginalMimicSuccessfully()
    {
        //change number of upvotes to 5
        $model = Mimic::find(1);
        $model->upvote = 5;
        $model->save();

        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'upvotes',
        ])
        ->assertJson([
            'type' => 'upvoted',
            'upvotes' => 6,

        ])
        ->assertStatus(200); 
    }

    public function testDownvoteOriginalMimicSuccessfully()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'upvotes',
        ])
        ->assertJson([
            'type' => 'downvoted',
            'upvotes' => 5,
        ])
        ->assertStatus(200); 

        //return back to default number of upvotes
        $model = Mimic::find(1);
        $model->upvote = 123456789;
        $model->save();
        
    }

    public function testUpvoteResponseMimicSuccessfully()
    {
        //change number of upvotes to 5
        $model = MimicResponse::find(1);
        $model->upvote = 5;
        $model->save();

        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'upvotes',
        ])
        ->assertJson([
            'type' => 'upvoted',
            'upvotes' => 6,
        ])
        ->assertStatus(200); 
    }

    public function testDownvoteResponseMimicSuccessfully()
    {
        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data, 'v2');

        $response
        ->assertJsonStructure([
            'type',
            'upvotes',
        ])
        ->assertJson([
            'type' => 'downvoted',
            'upvotes' => 5,
        ])
        ->assertStatus(200); 

        //return back to default number of upvotes
        $model = MimicResponse::find(1);
        $model->upvote = 123456789;
        $model->save();
    }

    //--------------------------------Report--------------------------------
    public function testReportOriginalMimicSuccessfully()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/report', $data, 'v2');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 
    }
 
    public function testReportResponseMimicSuccessfully()
    {
        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/report', $data, 'v2');

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 
    }
 

    //--------------------------------List mimics--------------------------------
    public function testListMimicsOnMainScreenPageOne()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=1', $data, 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' =>
            [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'responses_count',
                        'i_am_following_you',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name'
                        ],
    
                    ],
                    'hashtags_flat',
                    'mimic_responses' => [
                        '*' => [
                            'id',
                            'username',
                            'profile_picture',
                            'user_id',
                            'mimic_type',
                            'upvote',
                            'file',
                            'file_url',
                            'video_thumb_url',
                            'aws_file',
                            'upvoted',
                            'i_am_following_you',
                        ]
                    ]
                ]
            ]   
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 9,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 9
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 9,
                        'username' => 'Desynchronized',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/9.jpg',
                        'user_id' => 9,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'd71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'file_url' => 'http://mimic.loc/files/user/9/1970/01/d71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'i_am_following_you' => false,
                        'responses_count' => 10,
                    ],
                    'hashtags' => [
                        [
                            'hashtag_id' => 43,
                            'hashtag_name' => '#videogame'
                        ],
                        [
                            'hashtag_id' => 44,
                            'hashtag_name' => '#shoot'
                        ]
                    ],
                    'hashtags_flat' => '#videogame #shoot',
                    'mimic_responses' => [
                        [
                            'id' => 80,
                            'username' => 'piperpilot32',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/19.jpg',
                            'user_id' => 19,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'file_url' => 'http://mimic.loc/files/user/19/1970/01/0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/19/1970/01/f319af632ddaaedd97a79f01e958fb04.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ]
                    ]
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testIfThereIsntAnyMimicsOnMainScreen()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=100', $data, 'v2');

        $response
        ->assertJsonStructure([
            'mimics'
        ])
        ->assertJson([
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    public function testListOriginalMimicsOnMainScreenOfSpecificUser()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=1&user_id=1&original_mimic_id=1', $data, 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'responses_count',
                        'i_am_following_you',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name',
                        ]
                    ],
                    'hashtags_flat',
                    'mimic_responses' => [
                        '*' => [
                            'id',
                            'username',
                            'profile_picture',
                            'user_id',
                            'mimic_type',
                            'upvote',
                            'file',
                            'file_url',
                            'video_thumb_url',
                            'aws_file',
                            'upvoted',
                            'i_am_following_you',
                        ]
                    ]
                ]
            ]
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 1
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 1,
                        'username' => 'AndrewCG',
                        'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
                        'user_id' => 1,
                        'mimic_type' => 'video',
                        'upvote' => '123M',
                        'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg',
                        'aws_file' => null,
                        'upvoted' => 0,
                        'responses_count' => 11,
                        'i_am_following_you' => false,
                    ],
                    'hashtags' => [
                        [
                            'hashtag_id' => 12,
                            'hashtag_name' => '#beatbox'
                        ],
                        [
                            'hashtag_id' => 13,
                            'hashtag_name' => '#box'
                        ],
                        [
                            'hashtag_id' => 14,
                            'hashtag_name' => '#beat'
                        ],
                        [
                            'hashtag_id' => 15,
                            'hashtag_name' => '#music'
                        ]
                    ],
                    'hashtags_flat' => '#beatbox #box #beat #music',
                    'mimic_responses' => [
                        [
                            'id' => 11,
                            'username' => 'hogwartsthestral',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/12.jpg',
                            'user_id' => 12,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'file_url' => 'http://mimic.loc/files/user/12/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/12/1970/01/c7a2baecad36742d9cfdd40a52c7e6f5.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ]
                    ]
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testDisplayResponseMimicOfSpecificUserWithItsOriginalMimicOnMainScreen()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=1&user_id=1&original_mimic_id=1&response_mimic_id=3', $data, 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'responses_count',
                        'i_am_following_you',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name',
                        ]
                    ],
                    'hashtags_flat',
                    'mimic_responses' => [
                        '*' => [
                            'id',
                            'username',
                            'profile_picture',
                            'user_id',
                            'mimic_type',
                            'upvote',
                            'file',
                            'file_url',
                            'video_thumb_url',
                            'aws_file',
                            'upvoted',
                            'i_am_following_you',
                        ]
                    ]
                ]
            ]

        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 1
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 1,
                        'username' => 'AndrewCG',
                        'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg',
                        'user_id' => 1,
                        'mimic_type' => 'video',
                        'upvote' => '123M',
                        'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg',
                        'aws_file' => null,
                        'upvoted' => 0,
                        'responses_count' => 11,
                        'i_am_following_you' => false,
                    ],
                    'hashtags' => [
                        [
                            'hashtag_id' => 12,
                            'hashtag_name' => '#beatbox'
                        ],
                        [
                            'hashtag_id' => 13,
                            'hashtag_name' => '#box'
                        ],
                        [
                            'hashtag_id' => 14,
                            'hashtag_name' => '#beat'
                        ],
                        [
                            'hashtag_id' => 15,
                            'hashtag_name' => '#music'
                        ]
                    ],
                    'hashtags_flat' => '#beatbox #box #beat #music',
                    'mimic_responses' => [
                        [
                            'id' => 3,
                            'username' => 'Cognizant',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/4.jpg',
                            'user_id' => 4,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'file_url' => 'http://mimic.loc/files/user/4/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/4/1970/01/fa66664ccc90eaebc38daa2829e73b0e.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ],
                        [
                            'id' => 11,
                            'username' => 'hogwartsthestral',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/12.jpg',
                            'user_id' => 12,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'file_url' => 'http://mimic.loc/files/user/12/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/12/1970/01/c7a2baecad36742d9cfdd40a52c7e6f5.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ],
                    ],
                ],
            ],

        ])
        ->assertStatus(200);
    }

    //--------------------------------Load more original mimic's responses--------------------------------
    public function testLoadMoreResponsesForOriginalMimicOnMainScreen()
    {
        for($i = 0; $i < 50; $i++) {
            MimicResponse::create([
                'user_id' => 2,
                'original_mimic_id' => 1,
                'file' => 'xyz.jpg',
                'mimic_type' => 2,
                'upvote' => 123456789
            ]);
        }
        
        $data = [];

        $response = $this->doGet('mimic/load-responses?page=2&original_mimic_id=1', $data, 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'i_am_following_you',
                    ]
                ]
            ]
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 61,
                    'per_page' => 30,
                    'current_page' => 2,
                    'last_page' => 3,
                    'next_page_url' => 'http://mimic.loc/api/mimic/load-responses?page=3',
                    'prev_page_url' => 'http://mimic.loc/api/mimic/load-responses?page=1',
                    'has_more_pages' => true,
                    'first_item' => 31,
                    'last_item' => 60
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 100,
                        'username' => 'beachdude',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                        'user_id' => 2,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'xyz.jpg',
                        'file_url' => 'http://mimic.loc/files/user/2/'.date('Y').'/'.date('m').'/xyz.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'i_am_following_you' => false,
                    ]
                ],
                [
                    'mimic' => [
                        'id' => 99,
                        'username' => 'beachdude',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                        'user_id' => 2,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'xyz.jpg',
                        'file_url' => 'http://mimic.loc/files/user/2/'.date('Y').'/'.date('m').'/xyz.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'i_am_following_you' => false,
                    ]
                ],
                [
                    'mimic' => [
                        'id' => 98,
                        'username' => 'beachdude',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                        'user_id' => 2,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'xyz.jpg',
                        'file_url' => 'http://mimic.loc/files/user/2/'.date('Y').'/'.date('m').'/xyz.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'i_am_following_you' => false,
                    ]
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testLoadMoreResponsesForOriginalMimicOnMainScreenNoMoreResponses()
    {

        $data = [];

        $response = $this->doGet('mimic/load-responses?page=100&original_mimic_id=1', $data, 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics'
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 61,
                    'per_page' => 30,
                    'current_page' => 100,
                    'last_page' => 3,
                    'next_page_url' => null,
                    'prev_page_url' => 'http://mimic.loc/api/mimic/load-responses?page=99',
                    'has_more_pages' => false,
                    'first_item' => null,
                    'last_item' => null
                ]
            ],
            'mimics' => []
        ])
        ->assertStatus(200); 
    }


    public function testGetMimicsFromPeopleYouFollow()
    { 
        //add some followers
        foreach ([1, 3] as $following) {
            Follow::create([
                'followed_by' => 96,
                'following' => $following
            ]);
        }
        
        
        $response = $this->doGet('mimic/list?page=1&order_by=people_you_follow', [], 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'i_am_following_you',
                        'responses_count',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name'
                        ],
                    ],
                    'hashtags_flat',
                    'mimic_responses'
                ]
            ]
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 2,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 2
                ]
            ],
            'mimics' => [
                [     
                    'mimic' => [ 
                        'id' => 3, 
                        'username' => 'Chrisburke04', 
                        'profile_picture' => 'http://mimic.loc/files/hr/female/3.jpg', 
                        'user_id' => 3, 
                        'mimic_type' => 'picture', 
                        'upvote' => '123M', 
                        'file' => '7372ad0a28e9428413cbd41abb6433e5.jpg', 
                        'file_url' => 'http://mimic.loc/files/user/3/1970/01/7372ad0a28e9428413cbd41abb6433e5.jpg', 
                        'video_thumb_url' => null, 
                        'aws_file' => null, 
                        'upvoted' => 0, 
                        'i_am_following_you' => true, 
                        'responses_count' => 10,
                    ], 
                    'hashtags' => [ 
                        [ 
                            'hashtag_id' => 20, 
                            'hashtag_name' => '#happydog' 
                        ], 
                        [ 
                            'hashtag_id' => 21, 
                            'hashtag_name' => '#dog' 
                        ], 
                        [ 
                            'hashtag_id' => 22, 
                            'hashtag_name' => '#dogs' 
                        ], 
                        [ 
                            'hashtag_id' => 23, 
                            'hashtag_name' => '#cutedogs' 
                        ], 
                        [ 
                            'hashtag_id' => 24, 
                            'hashtag_name' => '#cute' 
                        ] 
                    ], 
                    'hashtags_flat' => '#happydog #dog #dogs #cutedogs #cute', 
                    'mimic_responses' => [ 
                        [ 
                            'id' => 26, 
                            'username' => 'Jrfoley5', 
                            'profile_picture' => 'http://mimic.loc/files/hr/female/13.jpg', 
                            'user_id' => 13, 
                            'mimic_type' => 'picture', 
                            'upvote' => '123M', 
                            'file' => '7372ad0a28e9428413cbd41abb6433e5.jpg', 
                            'file_url' => 'http://mimic.loc/files/user/13/1970/01/7372ad0a28e9428413cbd41abb6433e5.jpg', 
                            'video_thumb_url' => null, 
                            'aws_file' => null, 
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ] 
                    ] 
                ], 
                [ 
                    'mimic' => [ 
                        'id' => 1, 
                        'username' => 'AndrewCG', 
                        'profile_picture' => 'http://mimic.loc/files/hr/male/1.jpg', 
                        'user_id' => 1, 
                        'mimic_type' => 'video', 
                        'upvote' => '123M', 
                        'file' => '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4', 
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4', 
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg', 
                        'aws_file' => null, 
                        'upvoted' => 0, 
                        'i_am_following_you' => true, 
                        'responses_count' => 61 
                    ], 
                    'hashtags' => [ 
                        [ 
                            'hashtag_id' => 12, 
                            'hashtag_name' => '#beatbox' 
                        ], 
                        [ 
                            'hashtag_id' => 13, 
                            'hashtag_name' => '#box' 
                        ], 
                        [ 
                            'hashtag_id' => 14, 
                            'hashtag_name' => '#beat' 
                        ], 
                        [ 
                            'hashtag_id' => 15, 
                            'hashtag_name' => '#music' 
                        ] 
                    ], 
                    'hashtags_flat' => '#beatbox #box #beat #music', 
                    'mimic_responses' => [ 
                        [ 
                            'id' => 130, 
                            'username' => 'beachdude', 
                            'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg', 
                            'user_id' => 2, 
                            'mimic_type' => 'picture', 
                            'upvote' => '123M', 
                            'file' => 'xyz.jpg', 
                            'file_url' => 'http://mimic.loc/files/user/2/'.date('Y').'/'.date('m').'/xyz.jpg', 
                            'video_thumb_url' => null, 
                            'aws_file' => null, 
                            'upvoted' => 0 ,
                            'i_am_following_you' => false, 
                        ] 
                    ]
                ] 
            ]
        ])
        ->assertStatus(200); 
       
       
    }

    public function testGetRecentMimics()
    {
        $response = $this->doGet('mimic/list?page=1&order_by=recent', [], 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'responses_count',
                        'i_am_following_you',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name'
                        ],
                    ],
                    'hashtags_flat',
                    'mimic_responses'
                ]
            ]
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 9,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 9
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 9,
                        'username' => 'Desynchronized',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/9.jpg',
                        'user_id' => 9,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'd71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'file_url' => 'http://mimic.loc/files/user/9/1970/01/d71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'responses_count' => 10,
                        'i_am_following_you' => false,
                    ],
                    'hashtags' => [
                        [
                            'hashtag_id' => 43,
                            'hashtag_name' => '#videogame'
                        ],
                        [
                            'hashtag_id' => 44,
                            'hashtag_name' => '#shoot'
                        ],
                    ],
                    'hashtags_flat' => '#videogame #shoot',
                    'mimic_responses' => [
                        [
                            'id' => 80,
                            'username' => 'piperpilot32',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/19.jpg',
                            'user_id' => 19,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'file_url' => 'http://mimic.loc/files/user/19/1970/01/0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/19/1970/01/f319af632ddaaedd97a79f01e958fb04.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ],
                    ]
                ],
            ]
        ])
        ->assertStatus(200); 
    }

    public function testGetPopularMimics()
    {
        $response = $this->doGet('mimic/list?page=1&order_by=popular', [], 'v2');

        $response
        ->assertJsonStructure([
            'meta' => [
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                    'has_more_pages',
                    'first_item',
                    'last_item'
                ]
            ],
            'mimics' => [
                '*' => [
                    'mimic' => [
                        'id',
                        'username',
                        'profile_picture',
                        'user_id',
                        'mimic_type',
                        'upvote',
                        'file',
                        'file_url',
                        'video_thumb_url',
                        'aws_file',
                        'upvoted',
                        'responses_count',
                        'i_am_following_you',
                    ],
                    'hashtags' => [
                        '*' => [
                            'hashtag_id',
                            'hashtag_name'
                        ],
                    ],
                    'hashtags_flat',
                    'mimic_responses'
                ]
            ]
        ])
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 9,
                    'per_page' => 30,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                    'has_more_pages' => false,
                    'first_item' => 1,
                    'last_item' => 9
                ]
            ],
            'mimics' => [
                [
                    'mimic' => [
                        'id' => 9,
                        'username' => 'Desynchronized',
                        'profile_picture' => 'http://mimic.loc/files/hr/female/9.jpg',
                        'user_id' => 9,
                        'mimic_type' => 'picture',
                        'upvote' => '123M',
                        'file' => 'd71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'file_url' => 'http://mimic.loc/files/user/9/1970/01/d71934d20a65fd1ef32e914c0ad48c77.jpg',
                        'video_thumb_url' => null,
                        'aws_file' => null,
                        'upvoted' => 0,
                        'i_am_following_you' => false,
                        'responses_count' => 10,
                    ],
                    'hashtags' => [
                        [
                            'hashtag_id' => 43,
                            'hashtag_name' => '#videogame'
                        ],
                        [
                            'hashtag_id' => 44,
                            'hashtag_name' => '#shoot'
                        ]
                    ],
                    'hashtags_flat' => '#videogame #shoot',
                    'mimic_responses' => [
                        [
                            'id' => 80,
                            'username' => 'piperpilot32',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/19.jpg',
                            'user_id' => 19,
                            'mimic_type' => 'video',
                            'upvote' => '123M',
                            'file' => '0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'file_url' => 'http://mimic.loc/files/user/19/1970/01/0ad52c7c64e23d527a4c907c7deb211e.mp4',
                            'video_thumb_url' => 'http://mimic.loc/files/user/19/1970/01/f319af632ddaaedd97a79f01e958fb04.jpg',
                            'aws_file' => null,
                            'upvoted' => 0,
                            'i_am_following_you' => false,
                        ]
                    ]
                ],
            ]
        ])
        ->assertStatus(200);
    }

    //--------------------------------Upload mimics--------------------------------
    //original
    public function testSuccessfullyUploadImageOriginalMimic()
    {
        $path = public_path().'/files/user/4/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '24d23a82eb859b7832205fd83ce83a5c.jpg', 'image/jpg');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file];

        $response = $this->doPost('mimic/create', $data, 'v2');
        $responseJSON = TestCaseHelper::decodeResponse($response);
        $fileName = $responseJSON['mimic']['file'];

        $response
        ->assertJsonStructure([
            'mimic' => [
                'id',
                'username',
                'profile_picture',
                'user_id',
                'mimic_type',
                'upvote',
                'file',
                'file_url',
                'video_thumb_url',
                'aws_file',
                'upvoted',
                'responses_count',
                'i_am_following_you',
            ],
            'hashtags' => [
                '*' => 
                [
                    'hashtag_id',
                    'hashtag_name',
                ]
            ],
            'hashtags_flat',
            'mimic_responses',
        ])
        ->assertJson([
            'mimic' => [
                'id' => 10,
                'username' => 'xyz1234',
                'profile_picture' => 'https://graph.facebook.com/2042074229356674/picture?type=large',
                'user_id' => 96,
                'mimic_type' => 'picture',
                'upvote' => '1',
                'file' => $fileName,
                'file_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$fileName,
                'video_thumb_url' => null,
                'aws_file' => null,
                'upvoted' => 0,
                'responses_count' => null,
                'i_am_following_you' => false,
            ],
            'hashtags' => [
                [
                    'hashtag_id' => 45,
                    'hashtag_name' => '#skate'
                ],
                [
                    'hashtag_id' => 46,
                    'hashtag_name' => '#backflip'
                ],
                [
                    'hashtag_id' => 47,
                    'hashtag_name' => '#frontflip'
                ]
            ],
            'hashtags_flat' => '#skate #backflip #frontflip',
            'mimic_responses' => []
        ])
        ->assertStatus(200);

        Storage::disk('public')->assertExists('files/user/96/'.date('Y').'/'.date('m').'/'.$fileName);
    }

    public function testSuccessfullyUploadVideoOriginalMimic()
    {
        $path = public_path().'/files/user/4/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, 'fa66664ccc90eaebc38daa2829e73b0e.jpg', 'image/jpg');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file, 'video_thumbnail' => $videoThumbnail];

        $response = $this->doPost('mimic/create', $data, 'v2');
        $responseJSON = TestCaseHelper::decodeResponse($response);
        $fileName = $responseJSON['mimic']['file'];
        $array = explode('/', $responseJSON['mimic']['video_thumb_url']);
        $videoThumbFileName = end($array);

        $response
        ->assertJsonStructure([
            'mimic' => [
                'id',
                'username',
                'profile_picture',
                'user_id',
                'mimic_type',
                'upvote',
                'file',
                'file_url',
                'video_thumb_url',
                'aws_file',
                'upvoted',
                'responses_count',
                'i_am_following_you',
            ],
            'hashtags' => [
                '*' => 
                [
                    'hashtag_id',
                    'hashtag_name',
                ]
            ],
            'hashtags_flat',
            'mimic_responses',
        ])
        ->assertJson([
            'mimic' => [
                'username' => 'xyz1234',
                'profile_picture' => 'https://graph.facebook.com/2042074229356674/picture?type=large',
                'user_id' => 96,
                'mimic_type' => 'video',
                'upvote' => '1',
                'file' => $fileName,
                'file_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$fileName,
                'video_thumb_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$videoThumbFileName,
                'aws_file' => null,
                'upvoted' => 0,
                'responses_count' => null,
                'i_am_following_you' => false,
            ],
            'hashtags' => [
                [
                    'hashtag_id' => 45,
                    'hashtag_name' => '#skate'
                ],
                [
                    'hashtag_id' => 46,
                    'hashtag_name' => '#backflip'
                ],
                [
                    'hashtag_id' => 47,
                    'hashtag_name' => '#frontflip'
                ]
            ],
            'hashtags_flat' => '#skate #backflip #frontflip',
            'mimic_responses' => []
        ])
        ->assertStatus(200);

        Storage::disk('public')->assertExists('files/user/96/'.date('Y').'/'.date('m').'/'.$fileName);
    }

    public function testUploadVideoOriginalMimicVideoThumbnailNotSent()
    {
        $path = public_path().'/files/user/6/1970/01/';
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'Video thumbnail is required'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    public function testUploadVideoOriginalMimicVideoThumbnailWrongFileFormat()
    {
        $path = public_path().'/files/user/6/1970/01/';
        $file = TestCaseHelper::returnFakeFile('video.mp4');
        $videoThumbnail = TestCaseHelper::returnFakeFile('image.txt');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file, 'video_thumbnail' => $videoThumbnail];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'File should only be a photo (jpg or png).'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    public function testUploadVideoOriginalMimicVideoThumbnailSentAsText()
    {
        $path = public_path().'/files/user/6/1970/01/';
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file, 'video_thumbnail' => 'xyz'];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'File should be an image or a video',
                        'File should only be a photo (jpg or png).'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    //response
    public function testSuccessfullyUploadImageResponseMimic()
    {
        $path = public_path().'/files/user/5/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '0cf7dbb25cd97f09e826e36ec178e135.jpg', 'image/jpg');

        $data = ['mimic_file' => $file, 'original_mimic_id' => 1];

        $response = $this->doPost('mimic/create', $data, 'v2');
        $responseJSON = TestCaseHelper::decodeResponse($response);
        $fileName = $responseJSON['mimic']['file'];

        $response
        ->assertJsonStructure([
            'mimic' => [
                'id',
                'username',
                'profile_picture',
                'user_id',
                'mimic_type',
                'upvote',
                'file',
                'file_url',
                'video_thumb_url',
                'aws_file',
                'upvoted',
                'i_am_following_you',
            ]
        ])
        ->assertJson([
            'mimic' => [
                'id' => 131,
                'username' => 'xyz1234',
                'profile_picture' => 'https://graph.facebook.com/2042074229356674/picture?type=large',
                'user_id' => 96,
                'mimic_type' => 'picture',
                'upvote' => '1',
                'file' => $fileName,
                'file_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$fileName,
                'video_thumb_url' => null,
                'aws_file' => null,
                'upvoted' => null,
                'i_am_following_you' => null,
            ]
        ])
        ->assertStatus(200);

        Storage::disk('public')->assertExists('files/user/96/'.date('Y').'/'.date('m').'/'.$fileName);
    }

    public function testSuccessfullyUploadVideoResponseMimic()
    {
        $path = public_path().'/files/user/5/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '0cf4aa302cea84e9a15f2fe8a58a2f43.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, '7372ad0a28e9428413cbd41abb6433e5.jpg', 'image/jpg');

        $data = ['mimic_file' => $file, 'original_mimic_id' => 1, 'video_thumbnail' => $videoThumbnail];

        $response = $this->doPost('mimic/create', $data, 'v2');
        $responseJSON = TestCaseHelper::decodeResponse($response);
        $fileName = $responseJSON['mimic']['file'];
        $array = explode('/', $responseJSON['mimic']['video_thumb_url']);
        $videoThumbFileName = end($array);

        $response
        ->assertJsonStructure([
            'mimic' => [
                'id',
                'username',
                'profile_picture',
                'user_id',
                'mimic_type',
                'upvote',
                'file',
                'file_url',
                'video_thumb_url',
                'aws_file',
                'upvoted',
                'i_am_following_you',
            ]
        ])
        ->assertJson([
            'mimic' => [
                'id' => 132,
                'username' => 'xyz1234',
                'profile_picture' => 'https://graph.facebook.com/2042074229356674/picture?type=large',
                'user_id' => 96,
                'mimic_type' => 'video',
                'upvote' => '1',
                'file' => $fileName,
                'file_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$fileName,
                'video_thumb_url' => 'http://mimic.loc/files/user/96/'.date('Y').'/'.date('m').'/'.$videoThumbFileName,
                'aws_file' => null,
                'upvoted' => null,
                'i_am_following_you' => null,
            ]
        ])
        ->assertStatus(200);

        Storage::disk('public')->assertExists('files/user/96/'.date('Y').'/'.date('m').'/'.$fileName);
    }

    public function testUploadVideoResponseMimicVideoThumbnailNotSent()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'Video thumbnail is required'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    public function testUploadVideoResponseMimicVideoThumbnailWrongFileFormat()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');
        $videoThumbnail = TestCaseHelper::returnFakeFile('image.txt');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file, 'video_thumbnail' => $videoThumbnail];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'File should only be a photo (jpg or png).'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    public function testUploadVideoResponseMimicVideoThumbnailSentAsText()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = ['hashtags' => '#skate #backflip #frontflip', 'mimic_file' => $file, 'video_thumbnail' => 'xyz'];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'video_thumbnail'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'video_thumbnail' => [
                        'File should be an image or a video',
                        'File should only be a photo (jpg or png).'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }

    //general errors
    public function testUploadedOriginalOrResponseMimicIsNotVideoOrImage()
    {
        $file = TestCaseHelper::returnFakeFile('test.pdf');

        $data = ['mimic_file' => $file, 'original_mimic_id' => 1];
        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'mimic_file'
                ]
            ]
        ])
        ->assertJson([
            'error' => [
              'message' => '422 Unprocessable Entity',
              'errors' => [
                    'mimic_file' => [
                        'File should only be a photo (jpg or png) or a video (mp4).'
                    ]
                ]
            ]
        ])
        ->assertStatus(422);
    }

    public function testTryToUploadResponseButOriginalMimicIsDeleted()
    {
        $mimicId = 4;
        Mimic::find($mimicId)->delete();
        $file = TestCaseHelper::returnFakeFile('test.jpg');

        $data = ['mimic_file' => $file, 'original_mimic_id' => $mimicId];
        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
              'message',
            ]
        ])
        ->assertJson([
            'error' => [
              'message' => 'This Mimic has been deleted, you can\'t respond to this Mimic anymore',
            ]
        ])
        ->assertStatus(404);
    }

    public function testParameterForMimicFileIsNotSentForOriginalOrResponseForVideoOrImage()
    {
        $file = TestCaseHelper::returnFakeFile('image.jpg');

        $data = ['hashtags' => '#skate #backflip #frontflip'];

        $response = $this->doPost('mimic/create', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'errors' => [
                    'mimic_file'
                ],
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => '422 Unprocessable Entity',
                'errors' => [
                    'mimic_file' => [
                        'File should be an image or a video'
                    ]
                ],
            ]
        ])
        ->assertStatus(422);
    }


    //--------------------------------Delete mimics--------------------------------
    public function testDeleteOriginalMimicSuccessfully()
    {
        $mimicId = 2;
        $model = Mimic::find($mimicId);
        $data = [];

        $response = $this->doDelete('mimic/delete?mode=admin&original_mimic_id='.$mimicId, $data, 'v2');

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 

        //missing from local storage
        Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->file, $model, false, false));

        if($model->video_thumb) {
            Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->video_thumb, $model, false, false));
        }

        //missing from AWS
        if($model->aws_file) {
            $this->doGet($model->aws_file, $data, 'v2')->assertStatus(404);
        }

        if($model->aws_video_thumb) {
            $this->doGet($model->aws_video_thumb, $data, 'v2')->assertStatus(404);
        }
    }

    public function testDeleteResponseMimicSuccessfully()
    {
        $mimicId = 7;
        $model = MimicResponse::find($mimicId);
        $data = [];

        $response = $this->doDelete('mimic/delete?mode=admin&response_mimic_id='.$mimicId, $data, 'v2');

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 

        //missing from local storage
        Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->file, $model, false, false));

        if($model->video_thumb) {
            Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->video_thumb, $model, false, false));
        }

        //missing from AWS
        if($model->aws_file) {
            $this->doGet($model->aws_file, $data, 'v2')->assertStatus(404);
        }

        if($model->aws_video_thumb) {
            $this->doGet($model->aws_video_thumb, $data, 'v2')->assertStatus(404);

        }

    }

    public function testUserTriesToDeleteSomeoneElsesOriginalMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?original_mimic_id=2', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => 'This is not your Mimic, you can\'t delete it.'
            ]
        ])
        ->assertStatus(403); 
    }

    public function testUserTriesToDeleteSomeoneElsesResponseMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?response_mimic_id=2', $data, 'v2');

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => 'This is not your Mimic, you can\'t delete it.'
            ]
        ])
        ->assertStatus(403); 
    }
}