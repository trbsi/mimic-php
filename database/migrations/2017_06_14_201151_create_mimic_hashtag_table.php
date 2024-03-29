<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMimicHashtagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(db_table('mimic_hashtag'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->bigInteger('hashtag_id');
            $table->foreign('mimic_id')->references('id')->on(db_table('mimic'))->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('hashtag_id')->references('id')->on(db_table('hashtag'))->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['mimic_id', 'hashtag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('mimic_hashtag'));
    }
}
