<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('analysis_result_id');
            $table->foreign('analysis_result_id')
                  ->references('id')->on('analysis_results');               // 結果ID(外部キー)
            $table->string('user_name', 255)->charset('utf8');              // 表示名
            $table->string('user_account', 255)->charset('utf8');           // アカウント名
            $table->string('text', 255)->nullable()->charset('utf8');       // ツイート内容
            $table->integer('retweet_count')->nullable();                   // リツート数
            $table->integer('favorite_count')->nullable();                  // いいね数
            $table->dateTime('created_at');                                 // 投稿時間
            // $table->dateTimeTz('created_at');                            // 投稿時間
            $table->dateTime('insert_date')->useCurrent();                  // 追加日時
            $table->dateTime('update_date')
                  ->default(DB::raw('ON UPDATE CURRENT_TIMESTAMP'));        // 更新日時(datetime)
            $table->tinyInteger('delete_flag')->default(0);                 // 削除フラグ
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweets');
    }
}
