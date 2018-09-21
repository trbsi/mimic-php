<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(db_table('social_accounts'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->string('provider', 15); //facebook, twitter
            $table->string('provider_id', 30);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on(db_table('user'))->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(db_table('social_accounts'), function (Blueprint $table) {
            Schema::dropIfExists(db_table('social_accounts'));
        });
    }
}
