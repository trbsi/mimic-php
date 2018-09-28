<?php
namespace Tests\Functional\Api\V2\Mimic\Asserts;

use Tests\Functional\Api\V2\Mimic\Assert;

class UpvoteAssert
{
    /**
     * @return array
     */
    public function getUpvotesJsonStructureOnSuccess()
    {
        return [
            'meta' => [
                'pagination' => [
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ],
            'upvotes' => [
                '*' => [
                    'id',
                    'username',
                    'i_am_following_you',
                    'is_blocked',
                    'profile_picture',
                    'followers',
                    'number_of_mimics'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getNoUpvotesJsonStructureOnSuccess()
    {
        return [
            'meta' => [
                'pagination' => [
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ],
            'upvotes'
        ];
    }

    /**
     * @return array
     */
    public function getMimicUpvotesPageOneJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/Upvotes/mimic_upvotes_page_1.json');
    }

    /**
     * @return array
     */
    public function getMimicUpvotesPageTwoJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/Upvotes/mimic_upvotes_page_2.json');
    }

    /**
     * @return array
     */
    public function getResponseUpvotesPageOneJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/Upvotes/response_upvotes_page_1.json');
    }

    /**
     * @return array
     */
    public function getResponseUpvotesPageTwoJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/Upvotes/response_upvotes_page_2.json');
    }

    /**
     * @param array $data
     * @return array
     */
    public function getNoUpvotesJsonOnSuccess(array $data): array
    {
        return [
            'meta' => [
                'pagination' => $data['pagination']
            ],
            'upvotes' => []
        ];
    }
}
