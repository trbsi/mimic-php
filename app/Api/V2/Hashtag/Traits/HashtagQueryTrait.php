<?php
namespace App\Api\V2\Hashtag\Traits;

trait HashtagQueryTrait
{
    /**
     * Return top 10 hashtags
     *
     * @return collection
     */
    public function getTopTenHashtags()
    {
        return $this->orderBy('popularity', 'DESC')->limit(10)->get();
    }
}
