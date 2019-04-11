<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAnalysisResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->string('url', 255)->nullable()->charset('utf8mb4');  // URL
            $table->integer('retweet_count')->nullable();                // リツート数
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('retweet_count');
        });
    }
}
