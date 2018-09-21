<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(db_table('user_profile'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->text('bio')->nullable()->collation('utf8mb4_bin');
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on(db_table('user'))
            ->onDelete('cascade')
            ->onDelete('cascade');

            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('user_profile'));
    }
}
