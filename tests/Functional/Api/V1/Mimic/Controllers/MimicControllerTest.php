<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;
use App\Api\V1\Mimic\Models\Mimic;
use App\Api\V1\Mimic\Models\MimicResponse;
use JWTAuth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MimicControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    //Get mimics from a specific user
    public function testListMimicsFromUser()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=1', $data);

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
                        'video_thumb_url'
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
                        'upvote' => "123M",
                        'deleted_at' => null,
                        'created_at' => '1970-01-01 12:00:00',
                        'file_url' => 'http://mimic.loc/files/user/1/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4',
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg'
                    ]
                ]
        ])
        ->assertStatus(200);
        
    }

    public function testListMimicsFromUserThatDoesntHaveMimics()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=10', $data);

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

        $response = $this->doGet('mimic/user-mimics?user_id=2&get_responses=true', $data);

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
                        'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/bb4c28e90898e04516c86d398e165dee.jpg'
                    ]
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testListResponseMimicsFromUserThatDoesntHaveMimics()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=30', $data);

        $response
        ->assertJsonStructure([
                'mimics'
            ])
        ->assertJson([
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    //Upvote/downvote
    public function testUpvoteOriginalMimicSuccessfully()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'upvoted'
        ])
        ->assertStatus(200); 
    }

    public function testDownvoteOriginalMimicSuccessfully()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'downvoted'
        ])
        ->assertStatus(200); 
    }

    public function testUpvoteResponseMimicSuccessfully()
    {
        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'upvoted'
        ])
        ->assertStatus(200); 
    }

    public function testDownvoteResponseMimicSuccessfully()
    {
        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data);

        $response
        ->assertJsonStructure([
            'type'
        ])
        ->assertJson([
            'type' => 'downvoted'
        ])
        ->assertStatus(200); 
    }

    //Report
    public function testReportOriginalMimicSuccessfully()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/report', $data);

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

        $response = $this->doPost('mimic/report', $data);

        $response
        ->assertJsonStructure([
            'success'
        ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 
    }
 

    //List mimics
    public function testListMimicsOnMainScreenPageZero()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=0', $data);

        $response
        ->assertJsonStructure([
            'count',
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
                        'responses_count'
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
                            'upvoted'
                        ]
                    ]
                ]
            ]   
        ])
        ->assertJson([
            'count' => 9,
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
                        'responses_count' => 10
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
                            'upvoted' => 0
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

        $response = $this->doGet('mimic/list?page=10', $data);

        $response
        ->assertJsonStructure([
            'count',
            'mimics'
        ])
        ->assertJson([
            'count' => 0,
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    public function testListOriginalMimicsOnMainScreenOfSpecificUser()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=0&user_id=1&original_mimic_id=2', $data);

        $response
        ->assertJsonStructure([
            'count',
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
                        ]
                    ]
                ]
            ]
        ])
        ->assertJson([
            'count' => 1,
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
                        'responses_count' => 11
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
                            'upvoted' => 0
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

        $response = $this->doGet('mimic/list?page=0&user_id=1&response_mimic_id=1&original_mimic_id=1', $data);

        $response
        ->assertJsonStructure([
            'count',
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
                        ]
                    ]
                ]
            ]

        ])
        ->assertJson([
            'count' => 1,
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
                        'responses_count' => 11
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
                            'id' => 1,
                            'username' => 'beachdude',
                            'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                            'user_id' => 2,
                            'mimic_type' => 'picture',
                            'upvote' => '123M',
                            'file' => '46bcd7f8cc3373caea6b0efd888a5c2d.jpg',
                            'file_url' => 'http://mimic.loc/files/user/2/1970/01/46bcd7f8cc3373caea6b0efd888a5c2d.jpg',
                            'video_thumb_url' => null,
                            'aws_file' => null,
                            'upvoted' => 0
                        ]
                    ]
                ]
            ]

        ])
        ->assertStatus(200);
    }

    //Load more original mimic's responses
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

        $response = $this->doGet('mimic/load-responses?page=1&original_mimic_id=1', $data);

        $response
        ->assertJsonStructure([
            'mimics' => [
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
                ]
            ]
        ])
        ->assertJson([
            'mimics' => [
                [
                    'id' => 100,
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'user_id' => 2,
                    'mimic_type' => 'picture',
                    'upvote' => '123M',
                    'file' => 'xyz.jpg',
                    'file_url' => 'http://mimic.loc/files/user/2/2018/01/xyz.jpg',
                    'video_thumb_url' => null,
                    'aws_file' => null,
                    'upvoted' => 0
                ],
                [
                    'id' => 99,
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'user_id' => 2,
                    'mimic_type' => 'picture',
                    'upvote' => '123M',
                    'file' => 'xyz.jpg',
                    'file_url' => 'http://mimic.loc/files/user/2/2018/01/xyz.jpg',
                    'video_thumb_url' => null,
                    'aws_file' => null,
                    'upvoted' => 0
                ],
                [
                    'id' => 98,
                    'username' => 'beachdude',
                    'profile_picture' => 'http://mimic.loc/files/hr/female/2.jpg',
                    'user_id' => 2,
                    'mimic_type' => 'picture',
                    'upvote' => '123M',
                    'file' => 'xyz.jpg',
                    'file_url' => 'http://mimic.loc/files/user/2/2018/01/xyz.jpg',
                    'video_thumb_url' => null,
                    'aws_file' => null,
                    'upvoted' => 0
                ]
            ]
        ])
        ->assertStatus(200); 
    }

    public function testLoadMoreResponsesForOriginalMimicOnMainScreenNoMoreResponses()
    {

        $data = [];

        $response = $this->doGet('mimic/load-responses?page=10&original_mimic_id=1', $data);

        $response
        ->assertJsonStructure([
            'mimics'
        ])
        ->assertJson([
            'mimics' => []
        ])
        ->assertStatus(200); 
    }

    //Upload video thumb
    public function testSuccessfullyUploadVideoThumbForOriginalMimic()
    {
        $path = public_path().'/files/user/3/1970/01/24d23a82eb859b7832205fd83ce83a5c.jpg';
        $file = new UploadedFile(
            $path, 
            "24d23a82eb859b7832205fd83ce83a5c.jpg", 
            'image/png', 
            filesize($path), 
            null, 
            true);

        $data = ['original_mimic_id' => 1, 'file' => $file];

        $response = $this->doPost('mimic/upload-video-thumb', $data);
        $responseJSON = json_decode($response->getContent(), true);

        $response
        ->assertJsonStructure([
            'success',
            'video_thumb_url'
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertStatus(200);
        
        $array = explode("/", $responseJSON['video_thumb_url']);
        $videoThumbFileName = end($array);
        Storage::disk('public')->assertExists('files/user/1/1970/01/'.$videoThumbFileName);
    }

    public function testSuccessfullyUploadVideoThumbForResponseMimic()
    {
        $path = public_path().'/files/user/3/1970/01/254b169c6d3b87af59515398e4b17b4f.jpg';
        $file = new UploadedFile(
            $path, 
            "254b169c6d3b87af59515398e4b17b4f.jpg", 
            'image/png', 
            filesize($path), 
            null, 
            true);

        $data = ['response_mimic_id' => 1, 'file' => $file];

        $response = $this->doPost('mimic/upload-video-thumb', $data);
        $responseJSON = json_decode($response->getContent(), true);

        $response
        ->assertJsonStructure([
            'success',
            'video_thumb_url'
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertStatus(200);

        $array = explode("/", $responseJSON['video_thumb_url']);
        $videoThumbFileName = end($array);
        Storage::disk('public')->assertExists('files/user/2/1970/01/'.$videoThumbFileName);
    }

    public function testUploadVideoThumbForOriginalMimicFailedFileWasntSent()
    {
        $data = ['original_mimic_id' => 1];

        $response = $this->doPost('mimic/upload-video-thumb', $data);

        $response
        ->assertJsonStructure([
            'error'=> [
                'message',
            ]
        ])
        ->assertJson([
            'error'=> [
                'message'=> 'File should be an image',
            ]
        ])
        ->assertStatus(400);
    }

    public function testUploadVideoThumbForResponseMimicFailedFileWasntSent()
    {
        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upload-video-thumb', $data);

        $response
        ->assertJsonStructure([
            'error'=> [
                'message',
            ]
        ])
        ->assertJson([
            'error'=> [
                'message'=> 'File should be an image',
            ]
        ])
        ->assertStatus(400);
    }

    public function testUploadVideoThumbButFileIsNotPicture()
    {
        $path = public_path().'/files/user/3/1970/01/0cf4aa302cea84e9a15f2fe8a58a2f43.mp4';
        $file = new UploadedFile(
            $path, 
            "0cf4aa302cea84e9a15f2fe8a58a2f43.mp4", 
            'video/mp4', 
            filesize($path), 
            null, 
            true);

        $data = ['original_mimic_id' => 1, 'file' => $file];

        $response = $this->doPost('mimic/upload-video-thumb', $data);

        $response
        ->assertJsonStructure([
            'error'=> [
                'message',
            ]
        ])
        ->assertJson([
            'error'=> [
                'message'=> 'File should be an image',
            ]
        ])
        ->assertStatus(400);
    }

    //Upload mimics
    public function testSuccessfullyUploadImageOriginalMimic()
    {
    }


    //Delete mimics
    public function testDeleteOriginalMimicSuccessfully()
    {
        $mimicId = 3;
        $model = Mimic::find($mimicId);
        $oldModel = $model->replicate();
        $model->update(['user_id' => 96]);
        $data = [];

        $response = $this->doDelete('mimic/delete?original_mimic_id='.$mimicId, $data);

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 

        $model->update(['user_id' => $oldModel->user_id]);
    }

    public function testDeleteResponseMimicSuccessfully()
    {
        $mimicId = 3;
        $model = MimicResponse::find($mimicId);
        $oldModel = $model->replicate();
        $model->update(['user_id' => 96]);
        $data = [];

        $response = $this->doDelete('mimic/delete?response_mimic_id='.$mimicId, $data);

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 

        $model->update(['user_id' => $oldModel->user_id]);
    }

    public function testUserTriesToDeleteSomeoneElsesOriginalMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?original_mimic_id=2', $data);

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => "This is not your Mimic, you can't delete it."
            ]
        ])
        ->assertStatus(403); 
    }

    public function testUserTriesToDeleteSomeoneElsesResponseMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?response_mimic_id=2', $data);

        $response
        ->assertJsonStructure([
            'error' => [
                'message',
                'status_code'
            ]
        ])
        ->assertJson([
            'error' => [
                'message' => "This is not your Mimic, you can't delete it."
            ]
        ])
        ->assertStatus(403); 
    }
}