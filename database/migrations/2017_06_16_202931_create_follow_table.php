<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            //"followed_by" is following "following"
            $table->bigInteger('followed_by')->comment("user who is following another user"); //user who is following another user
            $table->bigInteger('following')->comment("user who is being followed"); //user who is being followed
            $table->timestamps();            
            $table->foreign('followed_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('following')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['followed_by', 'following']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow');
    }
}
