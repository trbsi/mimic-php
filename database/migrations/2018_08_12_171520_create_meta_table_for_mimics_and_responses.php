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
        Schema::create(db_table('mimic_meta'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->integer('width')->comment('In px');
            $table->integer('height')->comment('In px');
            $table->integer('thumbnail_width')->comment('In px')->nullable();
            $table->integer('thumbnail_height')->comment('In px')->nullable();
            $table->foreign('mimic_id')
            ->references('id')
            ->on(db_table('mimic'))
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->unique(['mimic_id']);
        });

        Schema::create(db_table('mimic_response_meta'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('mimic_id');
            $table->integer('width')->comment('In px');
            $table->integer('height')->comment('In px');
            $table->integer('thumbnail_width')->comment('In px')->nullable();
            $table->integer('thumbnail_height')->comment('In px')->nullable();
            $table->foreign('mimic_id')
            ->references('id')
            ->on(db_table('mimic_response'))
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->unique(['mimic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('mimic_meta'));
        Schema::dropIfExists(db_table('mimic_response_meta'));
    }
}
