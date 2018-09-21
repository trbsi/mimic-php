<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToMimicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(db_table('mimic'), function (Blueprint $table) {
            $table->text('description')->after('user_id')->nullable()->collation('utf8mb4_bin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(db_table('mimic'), function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
