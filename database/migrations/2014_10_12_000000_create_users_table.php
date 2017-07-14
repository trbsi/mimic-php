<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->string('email')->unique();
            $table->string('username', 50)->unique()->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->integer('followers')->default(0);
            $table->integer('following')->default(0);
            $table->integer('number_of_mimics')->default(0);
            $table->timestamps();
        });

        //https://laracasts.com/discuss/channels/general-discussion/fulltext-indexes-at-migrations
        \DB::statement('ALTER TABLE users ADD FULLTEXT INDEX ft_users_username (username);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
