<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_blocks_pivot', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('blocked_by')->comment('User who blocked');
            $table->bigInteger('user_id')->comment('User who is blocked');

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->foreign('blocked_by')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->unique(['blocked_by', 'user_id']);
            $table->unique(['user_id', 'blocked_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_blocks_pivot');
    }
}
