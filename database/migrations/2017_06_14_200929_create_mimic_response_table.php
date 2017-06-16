<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMimicResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mimic_response', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('original_mimic_id');
            $table->bigInteger('response_mimic_id');
            $table->timestamps();
            $table->foreign('original_mimic_id')->references('id')->on('mimics')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('response_mimic_id')->references('id')->on('mimics')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['response_mimic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mimic_response');
    }
}
