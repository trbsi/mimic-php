<?php

namespace App\Api\V1\Search\Controllers;

use App\Api\V1\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V1\Hashtag\Models\Hashtag;
use App\Api\V1\User\Models\User;
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
        if (substr($request->term, 0, 1) == "#") {
            $table = $hashtag->getTable();
            $match = 'name';
            $orderBy = 'popularity';
            $term = $request->term;
        } //search users
        elseif (substr($request->term, 0, 1) == "@") {
            $table = $user->getTable();
            $match = $orderBy = 'username';
            $term = substr($request->term, 1);
        } else {
            return [];
        }

        return DB::select("SELECT * FROM $table WHERE MATCH($match) AGAINST(? IN BOOLEAN MODE) ORDER BY $orderBy DESC", ["$term*"]);
    }
}
