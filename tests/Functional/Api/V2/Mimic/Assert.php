<?php
namespace Tests\Functional\Api\V2\Mimic;

use Tests\Assert\AssertInterface;
use Tests\Assert\AssertAbstract;
use Tests\Functional\Api\V2\Mimic\Asserts\UserMimicAssert;
use Tests\Functional\Api\V2\Mimic\Asserts\UpvoteDownvoteAssert;
use Tests\Functional\Api\V2\Mimic\Asserts\MimicAssert;

class Assert extends AssertAbstract implements AssertInterface
{
    public function __construct(
        UserMimicAssert $userMimicAssert,
        UpvoteDownvoteAssert $upvoteDownvoteAssert,
        MimicAssert $mimicAssert
    ) {
        $this->userMimicAssert = $userMimicAssert;
        $this->upvoteDownvoteAssert = $upvoteDownvoteAssert;
        $this->mimicAssert = $mimicAssert;
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array
    {
        switch ($type) {
            case 'user_mimic':
                return $this->userMimicAssert->getUserMimicsJsonStructureOnSuccess();
            case 'user_mimic_with_responses':
                return $this->userMimicAssert->getUserResponsesJsonStructureOnSuccess(true);
            case 'upvote_downvote':
                return $this->upvoteDownvoteAssert->getUpvoteDownvoteJsonStructureOnSuccess();
            case 'mimics':
                return $this->mimicAssert->getMimicsJsonStructureOnSuccess();
            case 'mimic':
                return $this->mimicAssert->getMimicJsonStructureOnSuccess();
            case 'load_more_responses':
                return $this->mimicAssert->getLoadMoreResponsesJsonStructureOnSuccess();
            case 'empty_mimics':
                return $this->mimicAssert->getEmptyMimicsJsonStructureOnSuccess();
            case 'response_mimic':
                return $this->mimicAssert->getResponseMimicJsonStructureOnSuccess();
        }
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {
        switch ($type) {
            case 'user_mimic':
                return $this->userMimicAssert->getUserMimicsJsonOnSuccess($data);
            case 'user_mimic_with_responses':
                return $this->userMimicAssert->getUserResponsesJsonOnSuccess($data, true);
            case 'upvote_downvote':
                return $this->upvoteDownvoteAssert->getUpvoteDownvoteJsonOnSuccess($data);
            case 'mimics':
                return $this->mimicAssert->getMimicsJsonOnSuccess();
            case 'user_mimics_with_original_on_first_place':
                return $this->mimicAssert->getUserMimicsWithOriginalOnFirstPlaceJsonOnSuccess();
            case 'user_mimics_with_response_and_its_original_mimic':
                return $this->mimicAssert->getUserMimicsWithResponseAndItsOriginalMimicJsonOnSuccess();
            case 'load_more_responses':
                return $this->mimicAssert->getLoadMoreResponsesJsonOnSuccess();
            case 'mimics_from_people_you_follow':
                return $this->mimicAssert->getMimicsFromPeopleYouFollowJsonOnSuccess();
            case 'popular_mimics':
                return $this->mimicAssert->getPopularMimicsJsonOnSuccess();
            case 'created_photo_mimic':
                return $this->mimicAssert->getCreatedPhotoMimicJsonOnSuccess($data);
            case 'created_video_mimic':
                return $this->mimicAssert->getCreatedVideoMimicJsonOnSuccess($data);
            case 'created_photo_response_mimic':
                return $this->mimicAssert->getCreatedPhotoResponseMimicJsonOnSuccess($data);
            case 'created_video_response_mimic':
                return $this->mimicAssert->getCreatedVideoResponseMimicJsonOnSuccess($data);
        }
    }
}
