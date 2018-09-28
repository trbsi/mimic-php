<?php

namespace App\Api\V2\User\Resources\Profile\JsonResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V2\Hashtag\JsonResources\HashtagResource;
use App\Api\V2\User\JsonResource\UserResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = UserResource::make($this)->resolve();
        $profile = [
            'profile' => [
                'bio' => $this->profile->bio,
                'hashtags' => HashtagResource::collection($this->profile->hashtags)
            ],
        ];
        
        return array_merge($user, $profile);
    }
}
