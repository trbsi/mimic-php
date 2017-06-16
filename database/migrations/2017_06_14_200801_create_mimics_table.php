<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMimicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mimics', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('media', 255);
            $table->tinyInteger('mimic_type'); //video/picture
            $table->tinyInteger('is_response'); //0/1 - is this mimic response or not
            $table->tinyInteger('is_private')->default(0); //0/1 - is this mimic private or not
            $table->integer('upvote')->default(0);
            $table->integer('user_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mimics');
    }
}
