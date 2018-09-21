<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHashtagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(db_table('hashtag'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->string('name', 255)->collation('utf8_general_ci');
            $table->integer('popularity');
            $table->timestamps();
        });

        //https://laracasts.com/discuss/channels/general-discussion/fulltext-indexes-at-migrations
        DB::statement('ALTER TABLE '.db_table('hashtag').' ADD FULLTEXT INDEX ft_hashtags_name (name);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(db_table('hashtag'));
    }
}
