<?php

namespace App\Api\V2\Follow\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowersResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->username,
            'i_am_following_you' => $this->i_am_following_you,
            'is_blocked' => $this->is_blocked,
            'profile_picture' => $this->profile_picture,
            'followers' => $this->followers,
            'number_of_mimics' => $this->number_of_mimics,
        ];
    }
}
