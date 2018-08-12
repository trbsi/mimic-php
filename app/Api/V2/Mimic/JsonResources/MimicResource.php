<?php

namespace App\Api\V2\Mimic\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V2\Mimic\Resource\Meta\JsonResources\MetaResource;
use App\Api\V2\Hashtag\JsonResources\HashtagResource;
use App\Api\V2\Mimic\Resource\Response\JsonResources\ResponseResource;
use Illuminate\Http\Resources\MissingValue;

class MimicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return 
        [
            'mimic' => [
                'id' => $this->id,
                'username' => $this->when($this->isLoaded($this->whenLoaded('user')), $this->user->username),
                'profile_picture' => $this->when($this->isLoaded($this->whenLoaded('user')), $this->user->profile_picture),
                'user_id' => $this->user_id,
                'mimic_type' => $this->mimic_type,
                'upvote' => $this->upvote,
                'file' => $this->file,
                'file_url' => $this->file_url,
                'video_thumb_url' => $this->video_thumb_url,
                'aws_file' => $this->aws_file,
                'upvoted' => $this->upvoted,
                'i_am_following_you' => $this->i_am_following_you,
                'created_at' => (int) strtotime($this->created_at),
                'meta' => new MetaResource($this->whenLoaded('meta')),
            ],
            'hashtags' => HashtagResource::collection($this->whenLoaded('hashtags')),
            'hashtags_flat' => $this->when($this->isLoaded($this->whenLoaded('hashtags')), $this->getHashtagsFlat()),
            'mimic_responses' => ResponseResource::collection($this->whenLoaded('responses')),
        ];
    }

    /**
     * Get flat hashtags
     *
     * @return string
     */
    private function getHashtagsFlat(): ?string
    {
        if (!$this->hashtags) {
            return null;
        }

        return $this->hashtags->implode('name', ' ');
    }

    /**
     * Check if resource is loaded or not
     *
     * @param mixed $resource
     * @return boolean
     */
    private function isLoaded($resource): bool
    {
        if ($resource instanceof MissingValue) {
            return false;
        }

        return true;
    }
}