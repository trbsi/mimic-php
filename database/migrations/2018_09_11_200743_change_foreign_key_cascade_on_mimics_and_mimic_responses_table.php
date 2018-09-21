<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignKeyCascadeOnMimicsAndMimicResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(db_table('mimic'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on(db_table('user'))
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::table(db_table('mimic_response'), function (Blueprint $table) {
            $table->dropForeign('mimic_responses_user_id_foreign');
            $table->foreign('user_id')
            ->references('id')
            ->on(db_table('user'))
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }
}
