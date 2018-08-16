<?php
namespace App\Api\V2\Mimic\Traits;

use DB;
use App\Helpers\SendPushNotification;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Api\V2\Mimic\JsonResources\MimicResource;

trait MimicTrait
{

    /**
     * Get file path for a mimic
     *
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
        }
        
        if ($includeRoot) {
            $prependPath = public_path();
        }

        if ($model !== null) {
            $Y = date("Y", strtotime($model->created_at));
            $m = date("m", strtotime($model->created_at));
        } else {
            $Y = date("Y");
            $m = date("m");
        }

        return $prependPath . Mimic::FILE_PATH . $user_id . "/" . $Y . "/" . $m . "/" . $file;
    }

    /**
     * Get absolute path to a file
     *
     * @param int $user_id
     * @param string $file
     * @param Mimic|MimicResponse $model
     * @return void
     */
    public function getAbsolutePathToFile(int $user_id, string $file, object $model): string
    {
        return $this->getFileOrPath($user_id, $file, $model, false, true);
    }

    /**
     * Get mimic model and return response
     *
     * @param  Mimic|MimicResponse $mimic Mimic or MimicResponse loaded result
     * @return array Generated mimic response
     */
    public function getSingleMimicResponseContent($mimic)
    {
        return new MimicResource($mimic);
    }

    /**
     * Get paginated response
     *
     * @param collection $paginatedModel Mimics/responses from the database taken with "->paginate()"
     * @return array
     */
    public function getPaginatedResponseContent($paginatedModel): array
    {
        return
        [
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
            'mimics' => MimicResource::collection($paginatedModel),
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
            case Mimic::TYPE_PHOTO:
                return Mimic::TYPE_PHOTO_STRING;
        }
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
                'i_am_following_you' => $mimic->i_am_following_you,
                'created_at' => (int) strtotime($mimic->created_at),
                'meta' => $mimic->meta,
            ];

        if ($mimic instanceof Mimic) {
            $extraParams['responses_count'] = $mimic->responses_count ?? 0;
        }

        return array_merge($standardResponse, $extraParams);
    }
}
