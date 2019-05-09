<?php

use App\BlackAnalysisWords;
use Illuminate\Database\Seeder;

class BlackAnalysisWordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // https://laravel.com/docs/5.8/eloquent#other-creation-methods
        $word_list = array(
                    array(0 => 'アイヌ系', 1 => 0),
                    array(0 => '合いの子', 1 => 0)
                );
        foreach ($word_list as $key => $record) {
            $match = BlackAnalysisWords::firstOrNew(array('analysis_ng_word' => $record[0]));
            $match->availability_flag = $record[1];
            $match->save();
        }
    }
}
