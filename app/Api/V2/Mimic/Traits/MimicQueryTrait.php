<?php
namespace App\Api\V2\Mimic\Traits;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Api\V2\Mimic\Models\MimicHashtag;
use App\Api\V2\Follow\Models\Follow;
use App\Api\V2\Mimic\Models\MimicUpvote;
use App\Api\V2\Mimic\Models\MimicResponseUpvote;
use Illuminate\Http\Request;
use App\Helpers\Constants;
use DB;

trait MimicQueryTrait
{
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
        $followTable = (new Follow)->getTable();

        return $this->mimicsQuery
        ->select("$mimicsTable.*")
        ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicsTable.id), 1, 0) AS upvoted")
        ->selectRaw("(SELECT COUNT(*) FROM $mimicResponseTable WHERE original_mimic_id = $mimicsTable.id) AS responses_count")
        ->selectRaw("IF(EXISTS(SELECT null FROM " . $followTable . " WHERE followed_by = " . $authUser->id . " AND following = ".$mimicsTable.".user_id),1,0) AS i_am_following_you")
        ->with(['mimicResponses' => function ($query) use ($authUser, $mimicResponseTable, $request, $followTable) {
            $query->select("$mimicResponseTable.*");
            //check if user upvoted this mimic response
            $query
            ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicResponseUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicResponseTable.id), 1, 0) AS upvoted")
            ->selectRaw("IF(EXISTS(SELECT null FROM " . $followTable . " WHERE followed_by = " . $authUser->id . " AND following = ".$mimicResponseTable.".user_id),1,0) AS i_am_following_you");
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
}
