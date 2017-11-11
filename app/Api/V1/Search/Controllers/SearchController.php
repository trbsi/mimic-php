<?php

namespace App\Api\V1\Search\Controllers;

use App\Api\V1\Controllers\BaseAuthController;
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
    public function search(Request $request)
    {
        //search hashtags
        if (substr($request->term, 0, 1) == "#") {
            $table = (new Hashtag)->getTable();
            $match = 'name';
            $orderBy = 'popularity';
            $term = $request->term;
        } //search users
        else if (substr($request->term, 0, 1) == "@") {
            $table = (new User)->getTable();
            $match = $orderBy = 'username';
            $term = substr($request->term, 1);
        } else {
            return [];
        }

        return DB::select("SELECT * FROM $table WHERE MATCH($match) AGAINST(? IN BOOLEAN MODE) ORDER BY $orderBy DESC", ["$term*"]);

    }
}
