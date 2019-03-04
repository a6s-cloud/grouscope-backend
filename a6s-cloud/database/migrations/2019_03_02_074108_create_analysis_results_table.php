<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalysisResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('analysis_start_date');                            // 分析開始日付
            // $table->dateTimeTz('analysis_start_date');
            $table->dateTime('analysis_end_date');                              // 分析終了日付
            $table->string('analysis_word', 255)->nullable()->charset('utf8');  // 分析ワード
            $table->tinyInteger('status')->default(0);                          // ステータス
            $table->integer('tweet_count')->nullable();                         // ツイート数
            $table->integer('favorite_count')->nullable();                      // いいね数
            $table->integer('user_count')->nullable();                          // ユーザ数
            $table->binary('image')->nullable();                                // wordcloud 画像
            $table->dateTime('insert_date')->userCurrent();                     // 追加日時
            $table->dateTime('update_date')
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));               // 追加日時
            $table->tinyInteger('delete_flag')->default(0);                     // 削除フラグ
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analysis_results');
    }
}
