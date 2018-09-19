<?php

namespace App\Api\V2\Mimic\Resources\Response\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Helpers\Constants;
use App\Api\V2\Mimic\Repositories\Get\GetUpvotesRepository;

class ResponseController extends BaseAuthController
{
    /**
     * Get people who upvoted specific mimic
     * @param  int $id                   
     * @param  GetUpvotesRepository $getUpvotesRepository 
     * @return Response                                     
     */
    public function upvotes($id, GetUpvotesRepository $GetUpvotesRepository) 
    {
        $result = $GetUpvotesRepository->getUpvotes($id, $this->authUser, Constants::MIMIC_RESPONSE);
        return response()->json($result);
    }
}
