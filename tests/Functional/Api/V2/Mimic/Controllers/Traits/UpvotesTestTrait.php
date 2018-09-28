<?php

namespace Tests\Functional\Api\V2\Mimic\Controllers\Traits;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;

trait UpvotesTestTrait
{
    //-------------------------------------ORIGINAL----------------------------
    public function testFirstPageOfUpvotesForOriginal()
    {
        $response = $this->doGet('mimic/1/upvotes?page=1', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimic_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'mimic_upvotes_page_1'))
        ->assertStatus(200);
    }

    public function testSecondPageOfUpvotesForOriginal()
    {
        $response = $this->doGet('mimic/1/upvotes?page=2', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimic_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'mimic_upvotes_page_2'))
        ->assertStatus(200);
    }

    public function testNoUpvotesForOriginal()
    {
        //remove all upvotes
        Mimic::find(1)->upvotes()->detach();

        $assertData = [
            'pagination' => [
              'current_page' => 1,
              'first_page_url' => 'http://mimic.loc/api/mimic/1/upvotes?page=1',
              'from' => null,
              'last_page' => 1,
              'last_page_url' => 'http://mimic.loc/api/mimic/1/upvotes?page=1',
              'next_page_url' => null,
              'path' => 'http://mimic.loc/api/mimic/1/upvotes',
              'per_page' => 30,
              'prev_page_url' => null,
              'to' => null,
              'total' => 0
            ]
        ];

        $response = $this->doGet('mimic/1/upvotes?page=1', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('mimic_no_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'mimic_no_upvotes'))
        ->assertStatus(200);
    }

    //--------------------------RESPONSE---------------------
    public function testFirstPageOfUpvotesForResponse()
    {
        $response = $this->doGet('mimic/response/1/upvotes?page=1', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('response_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'response_upvotes_page_1'))
        ->assertStatus(200);
    }

    public function testSecondPageOfUpvotesForResponse()
    {
        $response = $this->doGet('mimic/response/1/upvotes?page=2', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('response_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess([], 'response_upvotes_page_2'))
        ->assertStatus(200);
    }

    public function testNoUpvotesForResponse()
    {
        //remove all upvotes
        Response::find(1)->upvotes()->detach();

        $assertData = [
            'pagination' => [
              'current_page' => 1,
              'first_page_url' => 'http://mimic.loc/api/mimic/response/1/upvotes?page=1',
              'from' => null,
              'last_page' => 1,
              'last_page_url' => 'http://mimic.loc/api/mimic/response/1/upvotes?page=1',
              'next_page_url' => null,
              'path' => 'http://mimic.loc/api/mimic/response/1/upvotes',
              'per_page' => 30,
              'prev_page_url' => null,
              'to' => null,
              'total' => 0
            ]
        ];

        $response = $this->doGet('mimic/response/1/upvotes?page=1', []);
        $response
        ->assertJsonStructure($this->assert->getAssertJsonStructureOnSuccess('response_no_upvotes'))
        ->assertJson($this->assert->getAssertJsonOnSuccess($assertData, 'response_no_upvotes'))
        ->assertStatus(200);
    }
}
