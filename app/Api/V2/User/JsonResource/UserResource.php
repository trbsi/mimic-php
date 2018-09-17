<?php

namespace App\Api\V2\User\JsonResource;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'profile_picture' => $this->profile_picture,
            'followers' => $this->followers,
            'following' => $this->following,
            'number_of_mimics' => $this->number_of_mimics,
            'created_at' => strtotime($this->created_at),
            'updated_at' => strtotime($this->updated_at),
            'i_am_following_you' => $this->i_am_following_you ?? false,
            'is_blocked' => $this->is_blocked ?? false,
        ];
    }
}
