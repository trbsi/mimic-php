<?php
namespace App\Api\V2\Mimic\Traits;

use App\Helpers\SendPushNotification;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;

trait MimicTrait
{

    /**
     * Get file path for a mimic
     * @param  object $user_id User id
     * @param  object $model Mimic model
     * @param  string $file Mimic file
     * @param  boolean $includeDomain Whether to include domain in front of path or not
     * @param  boolean $includeRoot Whether to made path absolute or not
     * @return string Path to a file or a folder of a mimic
     */
    public function getFileOrPath($user_id, $file = null, $model = null, $includeDomain = false, $includeRoot = false)
    {
        $prependPath = false;
        if ($includeDomain) {
            $prependPath = env('APP_URL');
        } elseif ($includeRoot) {
            $prependPath = public_path();
        }

        if ($model != null) {
            $Y = date("Y", strtotime($model->created_at));
            $m = date("m", strtotime($model->created_at));
        } else {
            $Y = date("Y");
            $m = date("m");
        }

        return $prependPath . Mimic::FILE_PATH . $user_id . "/" . $Y . "/" . $m . "/" . $file;
    }

    /**
     * Get mimic model and return response
     *
     * @param  Mimic|MimicResponse $mimics Mimic or MimicResponse loaded result
     * @return array Generated mimic response
     */
    public function getMimicResponseContent($mimics)
    {
        $mimicsResponseContent = [];

        //check if this is collection of items got with get() method
        if (($mimics instanceof Collection && !$mimics->isEmpty()) || is_array($mimics)) {
            foreach ($mimics as $mimic) {
                $mimicsResponseContent[] = $this->generateContentForMimicResponse(
                    $mimic,
                    ($mimic->hashtags) ?? [],
                    ($mimic->mimicResponses) ?? []
                );
            }
        }
        //if this is single item taken with first()
        elseif ($mimics instanceof Collection === false && !empty($mimics)) {
            return $this->generateContentForMimicResponse(
                $mimics,
                ($mimics->hashtags) ??  [],
                ($mimics->mimicResponses) ?? []
            );
        }

        return $mimicsResponseContent;
    }

    /**
     * Get paginated response
     * 
     * @param collection $paginatedModel Mimics from the database taken with "->paginate()"
     * @return array
     */
    public function getPaginatedResponseContent($paginatedModel)
    {
        return 
        [
            'count' => $paginatedModel->total(), //@TODO remove, this will be legacy and replced with 'meta'
            'meta' => 
            [
                'pagination' => 
                [
                    'total' => $paginatedModel->total() ,
                    'per_page' => $paginatedModel->perPage(),
                    'current_page' => $paginatedModel->currentPage(),
                    'last_page' => $paginatedModel->lastPage(),
                    'next_page_url' => $paginatedModel->nextPageUrl(),
                    'prev_page_url' => $paginatedModel->previousPageUrl(),
                    'has_more_pages' => $paginatedModel->hasMorePages(),
                    'first_item' => $paginatedModel->firstItem(),
                    'last_item' => $paginatedModel->lastItem(),
                ]
            ],
            'mimics' => $this->getMimicResponseContent($paginatedModel->items()),
        ];
    }

    /**
     * Get Mimic type
     * @param  int $type 0/1
     * @return string "video/picture"
     */
    private function getMimicType($type)
    {
        switch ($type) {
            case Mimic::TYPE_VIDEO:
                return Mimic::TYPE_VIDEO_STRING;
                break;
            case Mimic::TYPE_PHOTO:
                return Mimic::TYPE_PHOTO_STRING;
                break;
        }
    }

    /**
     * generate mimic response
     * @param  [type] $mimic       [Mimic model]
     * @param  [type] $hashtags    [array of hashtags in form: [hashtag id] => hashtag name ]
     * @param  [type] $taggedUsers [array of usernames in form: [user id] => username] @TODO-TagUsers (future feature and needs to be tested)
     * @param  [type] $mimicResponses [all responses of a specific origina mimic, ordered descending by upvotes]
     * @return array Structured response
     */
    private function generateContentForMimicResponse($mimic, $hashtags, $mimicResponses, $taggedUsers = null)
    {
        $mimicStructure = $this->createMimicArrayStructure($mimic);

        //if this is mimic reponse just return that mimic without hashtags or mimic_responses
        if ($mimic instanceof MimicResponse) {
            return ['mimic' => $mimicStructure];
        }

        $hashtagsStructure = [];

        //it could be an array generated with  checkTags
        if (is_array($hashtags)) {
            foreach ($hashtags as $hashtag_id => $hashtag_name) {
                $hashtagsStructure[] =
                    [
                        "hashtag_id" => $hashtag_id,
                        "hashtag_name" => $hashtag_name
                    ];
            }
        } //if it's object from database
        elseif (is_object($hashtags)) {
            foreach ($hashtags as $hashtag) {
                $hashtagsStructure[] =
                    [
                        "hashtag_id" => $hashtag->id,
                        "hashtag_name" => $hashtag->name,
                    ];
            }
        }

        //@TODO-TagUsers (future feature and needs to be tested)
        /*$taggedUsersTmp = [];
        //it could be an array generated with  checkTaggedUser
        if (is_array($taggedUsers)) {
            foreach ($taggedUsers as $user_id => $username) {
                $taggedUsersTmp[] =
                    [
                        "user_id" => $user_id,
                        "username" => $username,
                    ];
            }
        } //if it's object from database
        else if (is_object($taggedUsers)) {
            foreach ($taggedUsers as $taggedUser) {
                $taggedUsersTmp[] =
                    [
                        "user_id" => $taggedUser->id,
                        "username" => $taggedUser->username
                    ];
            }
        }*/

        $mimicResponsesStructure = [];
        //get all mimic responses
        foreach ($mimicResponses as $mimicResponse) {
            $mimicResponsesStructure[] = $this->createMimicArrayStructure($mimicResponse);
        }

        return
            [
                'mimic' => $mimicStructure,
                'hashtags' => $hashtagsStructure,
                'hashtags_flat' => implode(" ", array_pluck($hashtagsStructure, 'hashtag_name')),
                //'taggedUsers' => $taggedUsersTmp, @TODO-TagUsers (future feature and needs to be tested)
                'mimic_responses' => $mimicResponsesStructure
            ];
    }

    /**
     * create and return array structure for each mimic
     * @param  $mimic [Mimic model]
     * @return [array]        [structured array]
     */
    private function createMimicArrayStructure($mimic)
    {
        $extraParams = [];
        $standardResponse =
            [
                'id' => $mimic->id,
                'username' => $mimic->user->username,
                'profile_picture' => $mimic->user->profile_picture,
                'user_id' => $mimic->user_id,
                'mimic_type' => $mimic->mimic_type,
                'upvote' => $mimic->upvote,
                'file' => $mimic->file,
                'file_url' => $mimic->file_url,
                'video_thumb_url' => $mimic->video_thumb_url,
                'aws_file' => $mimic->aws_file,
                'upvoted' => $mimic->upvoted,
            ];

        if ($mimic instanceof Mimic) {
            $extraParams['responses_count'] = $mimic->responses_count ?? 0;
        }

        return array_merge($standardResponse, $extraParams);
    }
}
