<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCharcterUtf8mb4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE analysis_results CONVERT TO CHARACTER SET utf8mb4');
        DB::unprepared('ALTER TABLE tweets CONVERT TO CHARACTER SET utf8mb4');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE analysis_results CONVERT TO CHARACTER SET utf8');
        DB::unprepared('ALTER TABLE tweets CONVERT TO CHARACTER SET utf8');
    }
}
