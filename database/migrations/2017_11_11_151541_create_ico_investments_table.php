<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcoInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ico_investments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('investor_account_number', 100)->comment('Ether wallet account number');
            $table->string('mimicoins_bought', 100);
            $table->bigInteger('affiliate_id')->nullable();

            $table->string('transaction_id', 100)->nullable();
            $table->integer('phase')->nullable();
            $table->string('number_of_eth_to_pay', 100)->nullable()->comment('Amount we get');
            $table->string('other_account_number', 100)->nullable();
            $table->string('amount_to_send_to_other_account', 100)->nullable()->comment('mimicoins');
            $table->string('amount_to_send_to_investor', 100)->nullable()->comment('mimicoins');
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('ico_affiliates')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ico_investments');
    }
}
