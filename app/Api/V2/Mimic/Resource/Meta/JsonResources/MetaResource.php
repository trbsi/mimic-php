<?php

namespace App\Api\V2\Mimic\Resource\Meta\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource;

class MetaResource extends JsonResource
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
            'width' => $this->width,
            'height' => $this->height,
            'thumbnail_width' => $this->thumbnail_width,
            'thumbnail_height' => $this->thumbnail_height,
        ];
    }
}
