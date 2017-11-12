<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcoAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ico_affiliates', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('affiliate_code', 20)->unique();
            $table->string('account_number', 100)->unique()->comment('Ether wallet account number');
            $table->enum('affiliate_type', ['investor', 'guest']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ico_affiliates');
    }
}
