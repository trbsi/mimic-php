<?php

namespace App\Api\V2\Hashtag\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource;

class HashtagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hashtag_id' => $this->id,
            'hashtag_name' => $this->name,
        ];
    }
}