<?php
namespace App\Api\V2\Mimic\Traits;

use DB;
use App\Helpers\SendPushNotification;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Api\V2\Mimic\Models\MimicHashtag;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Models\MimicUpvote;
use App\Api\V2\Mimic\Models\MimicResponseUpvote;
use Illuminate\Http\Request;
use App\Helpers\Constants;

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
     * get all original mimics (latest or from followers) from the database, with relations
     * See this for help on how to get only X items from relation table using map()
     * https://laravel.io/forum/04-05-2014-eloquent-eager-loading-to-limit-for-each-post
     * https://stackoverflow.com/questions/31700003/laravel-4-eloquent-relationship-hasmany-limit-records
     * @param  Request $request Laravel request
     * @param  Object $authUser Authenticated user
     * @return Collection
     */
    public function buildQuery(Request $request, object $authUser)
    {
        $this->mimicsQuery = $this;

        $result = $this
        ->buildQueryFilter($request, $authUser)
        ->buildQueryCore($request, $authUser)
        ->paginate(Mimic::LIST_ORIGINAL_MIMICS_LIMIT);

        //paginate mimicResponses
        $result->map(function ($query) {
            $query->mimicResponses = $query->mimicResponses->take(Mimic::LIST_RESPONSE_MIMICS_LIMIT);
            return $query;
        });

        return $result;
    }

    /**
     * Build query filter such as filtering by most recetn, popular, hashtag...
     * 
     * @param  Request $request Laravel request
     * @param  Object $authUser Authenticated user
     * @return object
     */
    private function buildQueryFilter(Request $request, object $authUser): object
    {
        $mimicsTable = $this->getTable();
        //filter original mimics by a specific user
        if ($request->user_id) {
            //if a visitor clicks on user's profile and then on one of his mimics, get user's mimics but put the mimic he clicked on as the first in the list
            if ($request->original_mimic_id) {
                $this->mimicsQuery = $this->mimicsQuery->orderBy(DB::raw("$mimicsTable.id=$request->original_mimic_id"), 'DESC');
            }
            $this->mimicsQuery = $this->mimicsQuery->where("$mimicsTable.user_id", $request->user_id);
        } //filter by hashtag
        elseif ($request->hashtag_id) {
            $mimicHashtagTable = (new MimicHashtag)->getTable();
            $this->mimicsQuery = $this->mimicsQuery
                ->join($mimicHashtagTable, "$mimicHashtagTable.mimic_id", '=', "$mimicsTable.id")
                ->where('hashtag_id', $request->hashtag_id);
        } else if($request->order_by === Constants::ORDER_BY_PEOPLE_YOU_FOLLOW) {
            //get mimics from people you follow and your mimics
            $followTable = (new Follow)->getTable();
            $this->mimicsQuery = $this->mimicsQuery
                ->where(function($query) use($authUser) {
                    $query->where('user_id', $authUser->id)->orWhereRaw('follow.following IS NOT NULL');
                }) 
                ->leftJoin($followTable, function ($join) use ($followTable, $mimicsTable, $authUser) {
                    $join->on("$followTable.following", '=', "$mimicsTable.user_id");
                    $join->where('followed_by', $authUser->id);
                })
                ->orderBy(DB::raw("IF(ISNULL(follow.following) = 0 || user_id = $authUser->id, 0, 1)"), 'ASC') //I made this. Keep my mimics and mimics of people I follow on the first place ordered by most recent. After this just order by mimics.id DESC and it will order by most recent but it will keep my mimics and those of people I follow on the top
            ;
        } else if($request->order_by === Constants::ORDER_BY_POPULAR) {
            $orderByColumn = 'upvote';
        }

        //set order by. default order by recent
        $this->mimicsQuery = $this->mimicsQuery->orderBy($orderByColumn ?? $this->getTable().'.id', $orderByType ?? 'DESC');//then order by other recent mimics


        return $this;
    }

    /**
     * Build query core
     * 
     * @param  Request $request Laravel request
     * @param  Object $authUser Authenticated user
     * @return object
     */
    private function buildQueryCore($request, $authUser)
    {
        $mimicsTable = $this->getTable();
        $mimicResponseTable = (new MimicResponse)->getTable();

        return $this->mimicsQuery
        ->select("$mimicsTable.*")
        ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicsTable.id), 1, 0) AS upvoted")
        ->selectRaw("(SELECT COUNT(*) FROM $mimicResponseTable WHERE original_mimic_id = $mimicsTable.id) AS responses_count")
        ->with(['mimicResponses' => function ($query) use ($authUser, $mimicResponseTable, $request) {
            $query->select("$mimicResponseTable.*");
            //check if user upvoted this mimic response
            $query->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicResponseUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicResponseTable.id), 1, 0) AS upvoted");
            //get user info for mimicResponses
            $query->with('user');

            //first order by this specific id then by upvote
            //if someone clicked on response mimic on user's profile make this response on the first place
            if ($request->response_mimic_id) {
                $query->orderBy(DB::raw("$mimicResponseTable.id=$request->response_mimic_id"), 'DESC');
            }
            //load responses by upvotes
            $query->orderBy("upvote", "DESC");
            $query->orderBy("$mimicResponseTable.id", "DESC");
        }, 'user', 'hashtags', /*'mimicTagusers'*/])
        ->groupBy("$mimicsTable.id");
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
