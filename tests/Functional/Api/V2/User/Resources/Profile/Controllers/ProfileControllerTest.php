<?php

namespace Tests\Functional\Api\V2\User\Resources\Profile\Controllers;

use Tests\Functional\Api\V2\TestCaseV2;
use App\Api\V2\User\Models\User;
use Tests\Functional\Api\V2\User\Resources\Profile\Assert;

class ProfileControllerTest extends TestCaseV2
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
    
    public function testProfileSuccessfullyUpdated()
    {
    	$bio = '😁 Totally new BIO! 😀';
    	$data = [
			'bio' => $bio
		];

        $response = $this->doPut('user/profile', $data);
        $response->assertStatus(204);

        $user = User::find($this->loggedUserId);
        $this->assertEquals($bio, $user->profile->bio);
    }

    public function testBioIsNotString()
    {
    	$data = [
			'bio' => 123
		];

		$errorsStructure = [
			'bio'
		];

		$errors = [
			'bio' => [
                'Bio should be text'
            ]
        ];

        $response = $this->doPut('user/profile', $data);
        $response
        	->assertStatus(422)
        	->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        	->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors));
    }

    public function testBioIsTooLong()
    {
    	$data = [
			'bio' => str_repeat("This is test text. ", 1000),
		];

		$errorsStructure = [
			'bio'
		];

		$errors = [
			'bio' => [
                'Bio can be 1000 characters long'
            ]
        ];

        $response = $this->doPut('user/profile', $data);
        $response
        	->assertStatus(422)
        	->assertJsonStructure($this->assert->getAssertJsonStructureOnUnprocessableEntityError($errorsStructure))
        	->assertJson($this->assert->getAssertJsonOnUnprocessableEntityError($errors));

    }
}