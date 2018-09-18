<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorOnMimicsAndResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mimics_metas', function (Blueprint $table) {
            $table->string('color', 10)->nullable();
        });

        Schema::table('mimic_responses_metas', function (Blueprint $table) {
            $table->string('color', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mimics_metas', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('mimic_responses_metas', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
