<?php

namespace App\Api\V2\Follow\JsonResources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FollowersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'followers' => $this->collection,
        ];
    }
}