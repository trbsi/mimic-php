<?php

namespace App\Api\V2\Hashtag\Repositories\Post;

use App\Api\V2\Hashtag\Models\Hashtag;

final class CreateHashtagsRepository
{
    private const MAX_TAG_LENGTH = 50;

	/**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * Extract hashtags from string and save it in main and pivot table
     * 
     * @param string $tags
     * @param Mimic|Profile $model Saved model
     * @return array
     */
    public function extractAndSaveHashtags($string, $model): array
    {
        $hashtagsArray = [];

        if (preg_match_all("(#[a-zA-Z0-9]+)", $string, $hashtags)) {
            foreach ($hashtags[0] as $hashtag) {
                //if length of string is 1 continue becuase this regex catches string even it it's only "#"
                if (strlen($hashtag) === 1) {
                    continue;
                }

                if (strlen($hashtag) > self::MAX_TAG_LENGTH) {
                    $hashtag = substr($hashtag, 0, self::MAX_TAG_LENGTH);
                }

                $tag = Hashtag::updateOrCreate(['name' => $hashtag]);
                $tag->preventMutation = true;
                $tag->increment("popularity");

                $hashtagsArray[$tag->id] = $hashtag;
            }
        }

        //save pivot table, but sync, don't attach
        $model->hashtags()->sync(array_flip($hashtagsArray));

        return $hashtagsArray;
    }
}