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
        Schema::create(db_table('user_block_pivot'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('blocked_by')->comment('User who blocked');
            $table->bigInteger('user_id')->comment('User who is blocked');

            $table->foreign('user_id')
            ->references('id')
            ->on(db_table('user'))
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->foreign('blocked_by')
            ->references('id')
            ->on(db_table('user'))
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
        Schema::dropIfExists(db_table('user_block_pivot'));
    }
}
