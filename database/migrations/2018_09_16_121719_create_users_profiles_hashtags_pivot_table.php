<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersProfilesHashtagsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_profiles_hashtags_pivot', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('profile_id');
            $table->bigInteger('hashtag_id');

            $table->foreign('hashtag_id')
            ->references('id')
            ->on('hashtags')
            ->onDelete('cascade')
            ->onDelete('cascade');

            $table->foreign('profile_id')
            ->references('id')
            ->on('users_profiles')
            ->onDelete('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_profiles_hashtags_pivot');
    }
}
