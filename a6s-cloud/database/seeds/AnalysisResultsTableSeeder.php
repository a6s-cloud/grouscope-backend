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
        AnalysisResults::create([
            'analysis_start_date'   => '2019-04-22 00:00:00',
            'analysis_end_date'     => '2019-04-22 23:59:59',
            'status'                => 2,
            'analysis_word'         => '#NGT48',
            'tweet_count'           => 584,
            'favorite_count'        => 104,
            'user_count'            => 504,
            'image'                 => 'dummy',
            'insert_date'           => $dt->format('Y-m-d H:i:s'),
            'url'                   => 'https://github.com/nsuzuki7713/a6s-cloud-front/',
            'retweet_count'         => '120'
        ]);
    }
}
