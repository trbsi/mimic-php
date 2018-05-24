<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDeviceColumnInPushNotificationsTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notifications_token', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `push_notifications_token` CHANGE `device` `device` ENUM('ios','android') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_notifications_token', function (Blueprint $table) {
            //
        });
    }
}
