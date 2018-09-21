<?php
namespace App\Cron\PushNotifications;

use DB;

class RemoveOldPushTokens
{
    /**
     * Remove old push tokens
     *
     * @return void
     */
    public function run()
    {
        //current time - 7 days
        $date = date('Y-m-d H:i:s', time() - 604800);
        DB::statement(sprintf('DELETE FROM %s WHERE updated_at <= "%s"', db_table('push_notifications_token'), $date));
    }
}
