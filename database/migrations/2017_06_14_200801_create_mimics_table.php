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
        Schema::create(db_table('mimic'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->string('file', 255);
            $table->string('aws_file', 255)->nullable();
            $table->string('video_thumb', 255)->nullable();
            $table->string('aws_video_thumb', 255)->nullable();
            $table->tinyInteger('mimic_type'); //video/picture
            $table->tinyInteger('is_private')->default(0); //0/1 - is this mimic private or not
            $table->bigInteger('upvote')->default(1);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on(db_table('user'))->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('mimic'));
    }
}
