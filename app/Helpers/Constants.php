<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Constants
{
    const PUSH_TYPE_NEW_RESPONSE = 'push_type_new_response';
    const PUSH_TYPE_UPVOTE = 'push_type_upvote';

    //MIMICS
    public const ORDER_BY_RECENT = 'recent';
    public const ORDER_BY_POPULAR = 'popular';
    public const ORDER_BY_PEOPLE_YOU_FOLLOW = 'people_you_follow';

    public const UPVOTED = 'upvoted';
    public const DOWNVOTED = 'downvoted';
    public const BLOCKED = 'blocked';
    public const UNBLOCKED = 'unblocked';
    public const FOLLOWED = 'followed';
    public const UNFOLLOWED = 'unfollowed';

    /**
     * Get our social accounts
     * @return array Links to social accounts
     */
    public static function socialAccounts()
    {
        return [
            'facebook' => 'https://www.facebook.com/app.mimics/',
            'twitter' => 'https://twitter.com/app_mimic',
            //'reddit' => 'https://www.reddit.com/r/Mimic_app/',
            //'steemit' => 'https://steemit.com/@dariot/feed',
            //'telegram' => 'https://t.me/joinchat/Gw8zPA_0YttHagXJeBwbsw',
            'instagram' => 'https://www.instagram.com/app_mimic/',
        ];
    }
}
