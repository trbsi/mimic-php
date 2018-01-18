<?php

namespace App\Functional\Api\V1\Controllers;

use App\TestCase;
use App\Api\V1\Mimic\Models\Mimic;
use App\Api\V1\Mimic\Models\MimicResponse;
use JWTAuth;

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
                        'updated_at' => '1970-01-01 12:00:00',
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
                    'updated_at' => '1970-01-01 12:00:00',
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
                        'updated_at' => '1970-01-01 12:00:00',
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
 

    //Delete mimics
    public function testDeleteOriginalMimicSuccessfully()
    {
        Mimic::find(1)->update(['user_id' => 96]);
        $data = [];

        $response = $this->doDelete('mimic/delete?original_mimic_id=1', $data);

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 
    }

    public function testDeleteResponseMimicSuccessfully()
    {
        MimicResponse::find(1)->update(['user_id' => 96]);
        $data = [];

        $response = $this->doDelete('mimic/delete?response_mimic_id=1', $data);

        $response
        ->assertJsonStructure([
                'success'
            ])
        ->assertJson([
            'success' => true
        ])
        ->assertStatus(200); 
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