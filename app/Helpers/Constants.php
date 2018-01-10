<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Constants
{
    const PUSH_TYPE_NEW_RESPONSE = 'push_type_new_response';
    const PUSH_TYPE_UPVOTE = 'push_type_upvote';

    
    /**
     * Get our social accounts
     * @return array Links to social accounts
     */
    public static function socialAccounts()
    {
        return [
            'facebook' => 'https://www.facebook.com/HelloMimic/',
            'twitter' => 'https://twitter.com/Mimic_app_',
            //'reddit' => 'https://www.reddit.com/r/Mimic_app/',
            'steemit' => 'https://steemit.com/@dariot/feed',
            //'telegram' => 'https://t.me/joinchat/Gw8zPA_0YttHagXJeBwbsw',
            'instagram' => 'https://www.instagram.com/app_mimic/',
        ];
    }
}