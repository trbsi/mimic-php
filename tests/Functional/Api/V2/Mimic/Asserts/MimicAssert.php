<?php
namespace Tests\Functional\Api\V2\Mimic\Asserts;

use Tests\Functional\Api\V2\Mimic\Assert;

class MimicAssert
{
    /**
     * @return array
     */
    public function getMimicsJsonStructureOnSuccess(): array
    {
        return [
            'meta' => $this->meta(),
            'mimics' => [
                '*' => [
                    'mimic' => $this->mimic(),
                    'hashtags' => $this->hashtags(),
                    'mimic_responses' => $this->responses(),
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMimicJsonStructureOnSuccess(): array
    {
        return [
            'mimic' => $this->mimic(),
            'hashtags' => $this->hashtags(),
            'mimic_responses' => $this->responses(),
        ];
    }

    /**
     * @return array
     */
    public function getResponseMimicJsonStructureOnSuccess(): array
    {
        return [
            'mimic' => $this->mimic(),
        ];
    }

    /**
     * @return array
     */
    public function getEmptyMimicsJsonStructureOnSuccess(): array
    {
        return [
            'meta' => $this->meta(),
            'mimics'
        ];
    }

    public function getLoadMoreResponsesJsonStructureOnSuccess(): array
    {
        return [
             'mimics' => [
                '*' => [
                    'mimic' => $this->mimic(),
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMimicsJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/recent_mimics.json');
    }

    /**
     * @return array
     */
    public function getUserMimicsWithOriginalOnFirstPlaceJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/user_mimics_with_original_on_first_place.json');
    }

    /**
     * @return array
     */
    public function getUserMimicsWithResponseAndItsOriginalMimicJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/user_mimics_with_response_and_its_original_mimic.json');
    }

    /**
     * @return array
     */
    public function getLoadMoreResponsesJsonOnSuccess(): array
    {
        $from = ['{year}', '{month}', '"{created_at}"'];
        $to = [date('Y'), date('m'), strtotime(date('Y-m-d 00:00:00'))];

        return Assert::getDecodedJsonDataFromFileByAlteringFile(
            __DIR__.'/ExpectedResponses/load_more_responses.json',
            $from,
            $to
        );
    }

    /**
     * @return array
     */
    public function getMimicsFromPeopleYouFollowJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/mimics_from_people_you_follow.json');
    }

    /**
     * @return array
     */
    public function getPopularMimicsJsonOnSuccess(): array
    {
        return Assert::getDecodedJsonDataFromFile(__DIR__.'/ExpectedResponses/popular_mimics.json');
    }

    /**
     * @param  array $data Data from response (Created mimic)
     * @return array
     */
    public function getCreatedPhotoMimicJsonOnSuccess(array $data): array
    {
        $from = ['{year}', '{month}', '{file}', '"{created_at}"'];
        $to = [date('Y'), date('m'), $data['mimic']['file'], $data['mimic']['created_at']];

        return Assert::getDecodedJsonDataFromFileByAlteringFile(
            __DIR__.'/ExpectedResponses/created_photo_mimic.json',
            $from,
            $to
        );
    }

    /**
     * @param  array $data Data from response (Created mimic)
     * @return array
     */
    public function getCreatedPhotoResponseMimicJsonOnSuccess(array $data): array
    {
        $from = ['{year}', '{month}', '{file}', '"{created_at}"'];
        $to = [date('Y'), date('m'), $data['mimic']['file'], $data['mimic']['created_at']];

        return Assert::getDecodedJsonDataFromFileByAlteringFile(
            __DIR__.'/ExpectedResponses/created_photo_response_mimic.json',
            $from,
            $to
        );
    }


    /**
     * @param  array $data Data from response (Created mimic)
     * @return array
     */
    public function getCreatedVideoMimicJsonOnSuccess(array $data): array
    {
        $from = ['{year}', '{month}', '{file}', '"{created_at}"', '{video_thumb_url}'];
        $to = [date('Y'), date('m'), $data['mimic']['file'], $data['mimic']['created_at'], $data['mimic']['video_thumb_url']];

        return Assert::getDecodedJsonDataFromFileByAlteringFile(
            __DIR__.'/ExpectedResponses/created_video_mimic.json',
            $from,
            $to
        );
    }

    /**
     * @param  array $data Data from response (Created mimic)
     * @return array
     */
    public function getCreatedVideoResponseMimicJsonOnSuccess(array $data): array
    {
        $from = ['{year}', '{month}', '{file}', '"{created_at}"', '{video_thumb_url}'];
        $to = [date('Y'), date('m'), $data['mimic']['file'], $data['mimic']['created_at'], $data['mimic']['video_thumb_url']];

        return Assert::getDecodedJsonDataFromFileByAlteringFile(
            __DIR__.'/ExpectedResponses/created_video_response_mimic.json',
            $from,
            $to
        );
    }


    /**
     * @return array
     */
    private function meta(): array
    {
        return [
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
        ];
    }

    /**
     * @return array
     */
    private function mimic(): array
    {
        return [
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
            'created_at',
            'meta' => [
                'width',
                'height',
                'thumbnail_width',
                'thumbnail_height',
            ]
        ];
    }

    /**
     * @return array
     */
    private function hashtags(): array
    {
        return [
            '*' => [
                'hashtag_id',
                'hashtag_name',
            ],
        ];
    }

    /**
     * @return array
     */
    private function responses(): array
    {
        return [
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
                'created_at',
                'meta' => [
                    'width',
                    'height',
                    'thumbnail_width',
                    'thumbnail_height',
                ]
            ]
        ];
    }
}
