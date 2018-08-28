<?php
namespace App\Cron\PushNotifications;

use DB;

class RemoveOldPushTokens
{
    /**
     * Update hashtags popularty based on how many times that hastags has been used per mimic
     *
     * @return void
     */
    public function run()
    {
    	//current time - 7 days
    	$date = date('Y-m-d H:i:s', time() - 604800);
        DB::statement(sprintf('DELETE FROM push_notifications_token WHERE updated_at <= "%s"', $date));
    }
}
