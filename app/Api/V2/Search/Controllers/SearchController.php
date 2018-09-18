<?php

namespace App\Api\V2\Search\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Api\V2\Hashtag\Models\Hashtag;
use App\Api\V2\User\Models\User;
use App\Helpers\Constants;
use DB;
use App\Api\V2\Search\Repositories\Get\SearchRepository;

class SearchController extends BaseAuthController
{
    /**
     * Search for users or hashtags
     * @param  Request $requets
     * @return json Result
     */
    public function search(Request $request, SearchRepository $searchRepository)
    {
        $result = $searchRepository->search($request->all(), $this->authUser);
        return response()->json($result);
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
        $users = $user->getTopTenUsers($this->authUser);

        return response()->json([
            'hashtags' => $hashtags,
            'users' => $users,
        ]);
    }
}
