<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBountyHunters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bounty_hunters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->string('contribution_type', 100);
            $table->string('forum', 100);
            $table->string('forum_nickname', 100);
            $table->string('email', 100);
            $table->string('ethereum_address', 100);
            $table->string('reward', 100);
            $table->tinyInteger('approved')->default(0);
            $table->text('previous_work');
            $table->text('contribution_work')->nullable();
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
        Schema::dropIfExists('bounty_hunters');
    }
}
