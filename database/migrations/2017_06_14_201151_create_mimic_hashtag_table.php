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
        Schema::create('mimic_hashtag', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('mimic_id');
            $table->integer('hashtag_id');
            $table->timestamps();
            $table->foreign('mimic_id')->references('id')->on('mimics')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('hashtag_id')->references('id')->on('hashtags')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mimic_hashtag');
    }
}
