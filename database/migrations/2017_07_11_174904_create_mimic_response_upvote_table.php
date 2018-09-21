<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMimicResponseUpvoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(db_table('mimic_response_upvote'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->foreign('mimic_id')->references('id')->on(db_table('mimic_response'))->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on(db_table('user'))->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['mimic_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('mimic_response_upvote'));
    }
}
