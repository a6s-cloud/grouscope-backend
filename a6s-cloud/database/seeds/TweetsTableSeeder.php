<?php

use App\Tweets;
use Illuminate\Database\Seeder;

class TweetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テーブルデータの初期化
        Tweets::truncate();

        // ダミーデータの作成
        $faker = \Faker\Factory::create('ja_JP');
        $dt = new DateTime;
        for($i = 0; $i < 1000; $i++) {
            Tweets::create([
                'analysis_result_id'    => ($i % 10) + 1,
                'user_name'             => $faker->name,
                'user_account'          => "@user_${i}",
                'text'                  => "${i} 回目のツイート",
                'retweet_count'         => $i,
                'favorite_count'        => $i + 10,
                'created_at'            => $dt->format('Y-m-d H:i:s'),
                'delete_flag'           => 0
            ]);
        }
    }
}
