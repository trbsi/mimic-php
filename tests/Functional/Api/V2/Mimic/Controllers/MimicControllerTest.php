<?php

namespace Tests\Functional\Api\V2\Mimic\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use Tests\Functional\Api\V2\Mimic\Assert;
use Tests\TestCaseHelper;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use App\Api\V2\Mimic\Resources\Upvote\Models\Upvote as MimicUpvote;
use App\Api\V2\Mimic\Resources\Response\Resources\Upvote\Models\Upvote as ResponseUpvote;
use Illuminate\Support\Facades\Storage;
use Tests\Functional\Api\V2\Mimic\Helpers\MimicTestHelper;
use Tests\Functional\Api\V2\Mimic\Controllers\Traits\{UpvotesTestTrait};

class MimicControllerTest extends TestCaseV2
{
    use UpvotesTestTrait;

    /**
     * Should you write responses to json file or not
     *
     * @var boolean
     */
    private $writeToFile = false;

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
    
    //--------------------------------Get mimics from a specific user--------------------------------
    public function testListMimicsFromUser()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=1', $data);
        $assertData = [
            'id' => 1,
            'user_id' => 1,
            'file' => '1-1.mp4',
            'aws_file' => null,
            'video_thumb' => '1-1.jpg',
            'aws_video_thumb' => null,
            'mimic_type' => 'video',
            'is_private' => false,
            'upvote' => '123M',
            'deleted_at' => null,
            'created_at' => '1970-01-01 12:00:00',
            'file_url' => 'http://mimic.loc/files/user/1/1970/01/1-1.mp4',
            'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/1-1.jpg',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('user_mimic'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'user_mimic'))
        ->assertStatus(200);
    }

    public function testListMimicsFromUserThatDoesntHaveMimics()
    {
        $data = [];

        $response = $this->doGet('mimic/user-mimics?user_id=20', $data);

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
        $assertData = [
            'id' => 1,
            'user_id' => 2,
            'original_mimic_id' => 1,
            'file' => '2-1.mp4',
            'aws_file' => null,
            'video_thumb' => "2-1.jpg",
            'aws_video_thumb' => null,
            'mimic_type' => 'video',
            'upvote' => '123M',
            'deleted_at' => null,
            'created_at' => '1970-01-01 12:00:00',
            'updated_at' => '1970-01-01 12:00:00',
            'file_url' => 'http://mimic.loc/files/user/2/1970/01/2-1.mp4',
            'video_thumb_url' => 'http://mimic.loc/files/user/2/1970/01/2-1.jpg',
            'meta' => [
                'width' => 600,
                'height' => 900,
                'thumbnail_width' => null,
                'thumbnail_height' => null,
            ],
            'original_mimic' => [
                'id' => 1,
                'user_id' => 1,
                'file' => '1-1.mp4',
                'aws_file' => null,
                'video_thumb' => '1-1.jpg',
                'aws_video_thumb' => null,
                'mimic_type' => 'video',
                'is_private' => false,
                'upvote' => '123M',
                'deleted_at' => null,
                'created_at' => '1970-01-01 12:00:00',
                'updated_at' => '1970-01-01 12:00:00',
                'file_url' => 'http://mimic.loc/files/user/1/1970/01/1-1.mp4',
                'video_thumb_url' => 'http://mimic.loc/files/user/1/1970/01/1-1.jpg',
            ]
        ];
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('user_mimic_with_responses'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'user_mimic_with_responses'))
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

    //--------------------------------Upvote/downvote--------------------------------
    public function testUpvoteOriginalMimicSuccessfully()
    {
        $mimicId = 2;

        //change number of upvotes to 5
        $model = Mimic::find($mimicId);
        $model->upvote = 5;
        $model->save();

        $data = ['original_mimic_id' => $mimicId];

        $response = $this->doPost('mimic/upvote', $data);
        $assertData = [
            'type' => 'upvoted',
            'upvotes' =>'6',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('upvote_downvote'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'upvote_downvote'))
        ->assertStatus(200);
    }

    public function testDownvoteOriginalMimicSuccessfully()
    {
        $mimicId = 1;
        $model = Mimic::find($mimicId);
        $model->upvote = 5;
        $model->save();

        $data = ['original_mimic_id' => $mimicId];

        $response = $this->doPost('mimic/upvote', $data);
        $assertData = [
            'type' => 'downvoted',
            'upvotes' => '4',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('upvote_downvote'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'upvote_downvote'))
        ->assertStatus(200);
    }

    public function testUpvoteResponseMimicSuccessfully()
    {
        $mimicId = 2;
        //change number of upvotes to 5
        $model = Response::find($mimicId);
        $model->upvote = 5;
        $model->save();

        //delete upvote
        $model->upvotes()->detach($this->loggedUserId);

        $data = ['response_mimic_id' => $mimicId];

        $response = $this->doPost('mimic/upvote', $data);
        $assertData = [
            'type' => 'upvoted',
            'upvotes' => '6',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('upvote_downvote'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'upvote_downvote'))
        ->assertStatus(200);
    }

    public function testDownvoteResponseMimicSuccessfully()
    {
        $model = Response::find(1);
        $model->upvote = 5;
        $model->save();

        $data = ['response_mimic_id' => 1];

        $response = $this->doPost('mimic/upvote', $data);

        $assertData = [
            'type' => 'downvoted',
            'upvotes' => '4',
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('upvote_downvote'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'upvote_downvote'))
        ->assertStatus(200);
    }

    //--------------------------------Report--------------------------------
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
 

    //--------------------------------List mimics--------------------------------
    public function testListMimicsOnMainScreenPageOne()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=1', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'mimics'))
        ->assertStatus(200);
    }

    public function testIfThereIsntAnyMimicsOnMainScreen()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=100', $data);

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

        $response = $this->doGet('mimic/list?page=1&user_id=1&original_mimic_id=1', $data);
        $this->writeToFile('user_mimics_with_original_on_first_place', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'user_mimics_with_original_on_first_place'))
        ->assertStatus(200);
    }

    public function testDisplayResponseMimicOfSpecificUserWithItsOriginalMimicOnMainScreen()
    {
        $data = [];

        $response = $this->doGet('mimic/list?page=1&user_id=1&original_mimic_id=1&response_mimic_id=3', $data);
        $this->writeToFile('user_mimics_with_response_and_its_original_mimic', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'user_mimics_with_response_and_its_original_mimic'))
        ->assertStatus(200);
    }

    //--------------------------------Load more original mimic's responses--------------------------------
    public function testLoadMoreResponsesForOriginalMimicOnMainScreen()
    {
        for ($i = 0; $i < 50; $i++) {
            $mimic = Response::create([
                'user_id' => 2,
                'original_mimic_id' => 1,
                'file' => 'xyz.jpg',
                'mimic_type' => 2,
                'upvote' => 123456789,
            ]);

            $mimic->created_at = date('Y-m-d 00:00:00');
            $mimic->updated_at = date('Y-m-d 00:00:00');
            $mimic->save();

            $mimic->meta()->create([
                'height' => 300,
                'width' => 200,
                'thumbnail_height' => 300,
                'thumbnail_width' => 200,
            ]);
        }
        
        $data = [];

        $response = $this->doGet('mimic/load-responses?page=2&original_mimic_id=1', $data);
        $this->writeToFile('load_more_responses', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('load_more_responses'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'load_more_responses'))
        ->assertStatus(200);
    }

    public function testLoadMoreResponsesForOriginalMimicOnMainScreenNoMoreResponses()
    {
        $data = [];

        $response = $this->doGet('mimic/load-responses?page=100&original_mimic_id=1', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('empty_mimics'))
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 19,
                    'per_page' => 30,
                    'current_page' => 100,
                    'last_page' => 1,
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
                'followed_by' => $this->loggedUserId,
                'following' => $following
            ]);
        }
        
        
        $response = $this->doGet('mimic/list?page=1&order_by=people_you_follow', []);
        $this->writeToFile('mimics_from_people_you_follow', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'mimics_from_people_you_follow'))
        ->assertStatus(200);
    }

    public function testGetRecentMimics()
    {
        $response = $this->doGet('mimic/list?page=1&order_by=recent', []);
        $this->writeToFile('recent_mimics', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'mimics'))
        ->assertStatus(200);
    }

    public function testGetPopularMimics()
    {
        $response = $this->doGet('mimic/list?page=1&order_by=popular', []);
        $this->writeToFile('popular_mimics', $response);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimics'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'popular_mimics'))
        ->assertStatus(200);
    }

    //--------------------------------UPLOAD MIMICS--------------------------------
    //ORIGINAL
    public function testSuccessfullyUploadImageOriginalMimic()
    {
        $path = public_path().'/files/user/4/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '4-25.jpg', 'image/jpg');

        $data = [
            'description' => 'This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);
        $this->writeToFile('created_photo_mimic', $response);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileName = MimicTestHelper::getMimicFileName($responseArray);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimic'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($responseArray, 'created_photo_mimic'))
        ->assertStatus(200);

        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $fileName));
    }

    public function testSuccessfullyUploadVideoOriginalMimic()
    {
        $path = public_path().'/files/user/4/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '4-3.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, '4-3.jpg', 'image/jpg');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
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
        $this->writeToFile('created_video_mimic', $response);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileName = MimicTestHelper::getMimicFileName($responseArray);
        $videoThumbFileName = MimicTestHelper::getMimicVideoThumbnailName($responseArray);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimic'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($responseArray, 'created_video_mimic'))
        ->assertStatus(200);

        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $fileName));

        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $videoThumbFileName));
    }

    public function testUploadVideoOriginalMimicVideoThumbnailNotSent()
    {
        $path = public_path().'/files/user/6/1970/01/';
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);

        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.video_thumbnail_required')
            ]
        ];
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUploadVideoOriginalMimicVideoThumbnailWrongFileFormat()
    {
        $path = public_path().'/files/user/6/1970/01/';
        $file = TestCaseHelper::returnFakeFile('video.mp4');
        $videoThumbnail = TestCaseHelper::returnFakeFile('image.txt');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
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
        
        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.video_thumbnail_mimes_only_photo')
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUploadVideoOriginalMimicVideoThumbnailSentAsText()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'video_thumbnail' => 'xyz',
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);

        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.file_should_be_image_video'),
                __('api/mimic/validations.create.video_thumbnail_mimes_only_photo'),
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    //RESPONSE
    public function testSuccessfullyUploadImageResponseMimic()
    {
        $path = public_path().'/files/user/5/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '5-4.jpg', 'image/jpg');

        $data = [
            'mimic_file' => $file,
            'original_mimic_id' => 1,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);
        $this->writeToFile('created_photo_response_mimic', $response);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileName = MimicTestHelper::getMimicFileName($responseArray);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('response_mimic'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($responseArray, 'created_photo_response_mimic'))
        ->assertStatus(200);

        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $fileName));
    }

    public function testSuccessfullyUploadVideoResponseMimic()
    {
        $path = public_path().'/files/user/5/1970/01/';
        $file = TestCaseHelper::returnNewUploadedFile($path, '5-22.mp4', 'video/mp4');
        $videoThumbnail = TestCaseHelper::returnNewUploadedFile($path, '5-22.jpg', 'image/jpg');

        $data = [
            'mimic_file' => $file,
            'original_mimic_id' => 1,
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
        $this->writeToFile('created_video_response_mimic', $response);

        $responseArray = TestCaseHelper::decodeResponse($response);
        $fileName = MimicTestHelper::getMimicFileName($responseArray);
        $videoThumbFileName = MimicTestHelper::getMimicVideoThumbnailName($responseArray);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('response_mimic'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($responseArray, 'created_video_response_mimic'))
        ->assertStatus(200);

        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $fileName));
        Storage::disk('public')->assertExists(sprintf('files/user/%s/%s/%s/%s', $this->loggedUserId, date('Y'), date('m'), $videoThumbFileName));
    }

    public function testUploadVideoResponseMimicVideoThumbnailNotSent()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);
        
        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.video_thumbnail_required')
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }


    public function testUploadVideoResponseMimicVideoThumbnailWrongFileFormat()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');
        $videoThumbnail = TestCaseHelper::returnFakeFile('image.txt');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
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
        
        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.video_thumbnail_mimes_only_photo')
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUploadVideoResponseMimicVideoThumbnailSentAsText()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'video_thumbnail' => 'xyz',
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF',
            ],
        ];

        $response = $this->doPost('mimic/create', $data);

        $errors = [
            'video_thumbnail' => [
                __('api/mimic/validations.create.file_should_be_image_video'),
                __('api/mimic/validations.create.video_thumbnail_mimes_only_photo'),
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['video_thumbnail']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUploadOriginalVideoMimicMetaIsMissing()
    {
        $file = TestCaseHelper::returnFakeFile('video.mp4');
        $videoThumb = TestCaseHelper::returnFakeFile('image.jpg');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
            'original_mimic_id' => 1,
            'video_thumbnail' => $videoThumb,
        ];

        $response = $this->doPost('mimic/create', $data);

        $unprocessableEntities = ['meta.height', 'meta.width', 'meta.color', 'meta.thumbnail_height', 'meta.thumbnail_width']; 
        $errors = [
            'meta.height' => [
                __('api/mimic/validations.create.height_is_required')
            ],
            'meta.width' => [
                __('api/mimic/validations.create.width_is_required')
            ],
            'meta.color' => [
                __('api/mimic/validations.create.meta_color'),
            ],
            'meta.thumbnail_height' => [
                __('api/mimic/validations.create.thumb_height_is_required'),
            ],
            'meta.thumbnail_width' => [
                __('api/mimic/validations.create.thumb_width_is_required'),
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($unprocessableEntities))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testUploadOriginalImageMimicMetaIsMissing()
    {
        $file = TestCaseHelper::returnFakeFile('image.jpg');

        $data = [
            'description' => ' This is a description #skate #backflip #frontflip 😀 😝 😍',
            'mimic_file' => $file,
        ];

        $response = $this->doPost('mimic/create', $data);

        $unprocessableEntities = ['meta.height', 'meta.width', 'meta.color']; 
        $errors = [
            'meta.height' => [
                __('api/mimic/validations.create.height_is_required')
            ],
            'meta.width' => [
                __('api/mimic/validations.create.width_is_required')
            ],
            'meta.color' => [
                __('api/mimic/validations.create.meta_color'),
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($unprocessableEntities))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    //---------------------GENERAL ERRORS--------------------------
    public function testUploadedOriginalOrResponseMimicIsNotVideoOrImage()
    {
        $file = TestCaseHelper::returnFakeFile('test.pdf');

        $data = ['mimic_file' => $file, 'original_mimic_id' => 1];
        $response = $this->doPost('mimic/create', $data);
        
        $errors = [
            'mimic_file' => [
                    __('api/mimic/validations.create.file_mimes_only_photo_or_video')
                ]
        ];
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['mimic_file']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    public function testTryToUploadResponseButOriginalMimicIsDeleted()
    {
        $mimicId = 4;
        Mimic::find($mimicId)->delete();
        $file = TestCaseHelper::returnFakeFile('test.jpg');

        $data = [
            'mimic_file' => $file,
            'original_mimic_id' => $mimicId,
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
                'color' => '#FFFFFF'
            ],
        ];

        $response = $this->doPost('mimic/create', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('validation.mimic_is_deleted')))
        ->assertStatus(404);
    }

    public function testParameterForMimicFileIsNotSentForOriginalOrResponseForVideoOrImage()
    {
        $data = [
            'description' => '#skate #backflip #frontflip',
            'meta' => [
                'width' => 900,
                'height' => 600,
                'thumbnail_width' => 900,
                'thumbnail_height' => 600,
            ],
        ];

        $response = $this->doPost('mimic/create', $data);
        
        $errors = [
            'mimic_file' => [
                __('api/mimic/validations.create.file_should_be_image_video')
            ]
        ];

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError(['mimic_file']))
        ->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors))
        ->assertStatus(422);
    }

    //--------------------------------Delete mimics--------------------------------
    public function testDeleteOriginalMimicSuccessfully()
    {
        $mimicId = 2;
        $model = Mimic::find($mimicId);
        $data = [];

        $response = $this->doDelete('mimic/delete?mode=admin&original_mimic_id='.$mimicId, $data);

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

        if ($model->video_thumb) {
            Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->video_thumb, $model, false, false));
        }

        //missing from AWS
        if ($model->aws_file) {
            $this->doGet($model->aws_file, $data)->assertStatus(404);
        }

        if ($model->aws_video_thumb) {
            $this->doGet($model->aws_video_thumb, $data)->assertStatus(404);
        }
    }

    public function testDeleteResponseMimicSuccessfully()
    {
        $mimicId = 7;
        $model = Response::find($mimicId);
        $data = [];

        $response = $this->doDelete('mimic/delete?mode=admin&response_mimic_id='.$mimicId, $data);

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

        if ($model->video_thumb) {
            Storage::disk('public')->assertMissing($model->getFileOrPath($model->user_id, $model->video_thumb, $model, false, false));
        }

        //missing from AWS
        if ($model->aws_file) {
            $this->doGet($model->aws_file, $data)->assertStatus(404);
        }

        if ($model->aws_video_thumb) {
            $this->doGet($model->aws_video_thumb, $data)->assertStatus(404);
        }
    }

    public function testUserTriesToDeleteSomeoneElsesOriginalMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?original_mimic_id=2', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('mimic.delete.mimic_not_yours')))
        ->assertStatus(403);
    }

    public function testUserTriesToDeleteSomeoneElsesResponseMimic()
    {
        $data = [];

        $response = $this->doDelete('mimic/delete?response_mimic_id=2', $data);

        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnError())
        ->assertJson($this->assert->getAssertJsonOnError(trans('mimic.delete.mimic_not_yours')))
        ->assertStatus(403);
    }


    /**
     * Write responses in json file
     *
     * @param  string $file
     * @param  mixed $response
     * @return void
     */
    private function writeToFile(string $file, $response): void
    {
        if ($this->writeToFile) {
            file_put_contents(sprintf(__DIR__.'/../Asserts/ExpectedResponses/%s.json', $file), $response->getContent());
        }
    }
}
