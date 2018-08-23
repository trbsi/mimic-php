<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Constants
{
    public const ADMIN_EMAILS = ["ikova47@gmail.com", "dario.trbovic@yahoo.com"];

    //MIMICS
    public const ORDER_BY_RECENT = 'recent';
    public const ORDER_BY_POPULAR = 'popular';
    public const ORDER_BY_PEOPLE_YOU_FOLLOW = 'people_you_follow';
    public const MIMIC_ORIGINAL = 'original';
    public const MIMIC_RESPONSE = 'response';

    //PUSH NOTIFICATIONS
    //push parameters
    public const POSITION_SPLIT_SCREEN = 'split_screen';
    public const POSITION_USER_PROFILE = 'user_profile';
    public const MUTABLE_CATEGORY_USER = 'user';

    //ACTIONS
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
