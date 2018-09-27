<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAwsColumnsInMimicsAndResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mimics', function (Blueprint $table) {
            $table->renameColumn('aws_file', 'cloud_file');
            $table->renameColumn('aws_video_thumb', 'cloud_video_thumb');
        });

        Schema::table('mimic_responses', function (Blueprint $table) {
            $table->renameColumn('aws_file', 'cloud_file');
            $table->renameColumn('aws_video_thumb', 'cloud_video_thumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mimics', function (Blueprint $table) {
            $table->renameColumn('cloud_file', 'aws_file');
            $table->renameColumn('cloud_video_thumb', 'aws_video_thumb');
        });

        Schema::table('mimic_responses', function (Blueprint $table) {
            $table->renameColumn('cloud_file', 'aws_file');
            $table->renameColumn('cloud_video_thumb', 'aws_video_thumb');
        });
    }
}
