<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlackAnalysisWords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('black_analysis_words', function (Blueprint $table) {
            //
            $table->bigIncrements('id');
            $table->string('analysis_ng_word', 255)->unique()->charset('utf8mb4');      // NG ワード
            $table->boolean('availability_flag')->default(0);                           // 有効フラグ
            $table->dateTime('insert_date')->useCurrent();                              // 追加日時
            $table->dateTime('update_date')
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));  // 追加日時
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('black_analysis_words');
    }
}
