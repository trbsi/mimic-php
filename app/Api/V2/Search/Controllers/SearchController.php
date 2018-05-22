<?php

namespace App\Api\V2\Search\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\Hashtag\Models\Hashtag;
use App\Api\V2\User\Models\User;
use App\Helpers\Constants;
use DB;

class SearchController extends BaseAuthController
{
    /**
     * Search for users or hashtags
     * @param  Request $requets
     * @return json Result
     */
    public function search(Request $request, Hashtag $hashtag, User $user)
    {
        //search hashtags
        if (substr($request->term, 0, 1) === "#") {
            $table = $hashtag->getTable();
            $match = 'name';
            $orderBy = 'popularity';
            $term = $request->term;
            $model = $hashtag;
        } //search users
        elseif (substr($request->term, 0, 1) === "@") {
            $table = $user->getTable();
            $match = $orderBy = 'username';
            $term = substr($request->term, 1);
            $model = $user;
        } else {
            return [];
        }

        return $model->whereRaw("(MATCH($match) AGAINST(? IN BOOLEAN MODE))", ["$term*"])
            ->orderBy($orderBy, 'DESC')
            ->get();
    }

    /**
     * Get top 10 hashtags and users.
     * 
     * @param  Request $request
     * @return json
     */
    public function topHashtagsAndUsers(Request $request, Hashtag $hashtag, User $user)
    {
        $hashtags = $hashtag->getTopTenHashtags();
        $users = $user->getTopTenUsers();

        return response()->json([
            'hashtags' => $hashtags,
            'users' => $users,
        ]);
    }
}
