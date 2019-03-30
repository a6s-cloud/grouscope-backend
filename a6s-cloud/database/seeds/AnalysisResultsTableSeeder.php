<?php

use App\AnalysisResults;
use Illuminate\Database\Seeder;

class AnalysisResultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テーブルデータの初期化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AnalysisResults::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ダミーデータの作成
        $faker = \Faker\Factory::create('ja_JP');
        $dt = new DateTime;
        for($i = 0; $i < 100; $i++) {
            AnalysisResults::create([
                'analysis_start_date'   => $dt->format('Y-m-d H:i:s'),
                'analysis_end_date'     => $dt->format('Y-m-d H:i:s'),
                'status'                => $i % 4,
                'analysis_word'         => "${i} 回目の解析文章",
                'tweet_count'           => $i,
                'favorite_count'        => $i,
                'user_count'            => $i,
                'image'                 => $i,
                'insert_date'           => $dt->format('Y-m-d H:i:s')
            ]);
        }
    }
}
