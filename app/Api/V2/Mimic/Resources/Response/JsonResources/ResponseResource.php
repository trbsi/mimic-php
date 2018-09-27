<?php

namespace App\Api\V2\Mimic\Resources\Response\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V2\Mimic\Resources\Meta\JsonResources\MetaResource;

class ResponseResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->when($this->whenLoaded('user'), $this->user->username),
            'profile_picture' => $this->when($this->whenLoaded('user'), $this->user->profile_picture),
            'user_id' => $this->user_id,
            'mimic_type' => $this->mimic_type,
            'upvote' => $this->upvote,
            'file' => $this->file,
            'file_url' => $this->file_url,
            'video_thumb_url' => $this->video_thumb_url,
            'cloud_file' => $this->cloud_file,
            'upvoted' => $this->upvoted,
            'i_am_following_you' => $this->i_am_following_you,
            'created_at' => (int) strtotime($this->created_at),
            'meta' => new MetaResource($this->whenLoaded('meta')),
        ];
    }
}
