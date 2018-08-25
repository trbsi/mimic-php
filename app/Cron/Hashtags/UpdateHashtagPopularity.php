<?php
namespace App\Cron\Hashtags;

use DB;

class UpdateHashtagPopularity
{
    /**
     * Update hashtags popularty based on how many times that hastags has been used per mimic
     *
     * @return void
     */
    public function run()
    {
        DB::statement('UPDATE hashtags SET popularity = (SELECT COUNT(*) FROM mimic_hashtag WHERE hashtag_id=hashtags.id)');
    }
}
