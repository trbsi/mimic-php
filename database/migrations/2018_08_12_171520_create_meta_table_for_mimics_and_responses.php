<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTableForMimicsAndResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('mimic_response', 'mimic_responses');

        Schema::create('mimics_metas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->integer('width')->comment('In px');
            $table->integer('height')->comment('In px');
            $table->integer('thumbnail_width')->comment('In px')->nullable();
            $table->integer('thumbnail_height')->comment('In px')->nullable();
            $table->foreign('mimic_id')
            ->references('id')
            ->on('mimics')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('mimic_responses_metas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->integer('width')->comment('In px');
            $table->integer('height')->comment('In px');
            $table->integer('thumbnail_width')->comment('In px')->nullable();
            $table->integer('thumbnail_height')->comment('In px')->nullable();
            $table->foreign('mimic_id')
            ->references('id')
            ->on('mimic_responses')
            ->onUpdate('cascade')
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
        Schema::rename('mimic_responses', 'mimic_response');
        Schema::dropIfExists('mimics_metas');
        Schema::dropIfExists('mimic_responses_metas');
    }
}
