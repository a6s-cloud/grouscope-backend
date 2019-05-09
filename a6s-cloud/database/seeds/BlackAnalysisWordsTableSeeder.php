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
        if( ($handle = fopen(database_path() . '/csv/black_analysis_words.csv', 'r')) !== FALSE ) {
            while (($record = fgetcsv($handle)) !== FALSE ) {
                $match = BlackAnalysisWords::firstOrNew(array('analysis_ng_word' => $record[0]));
                $match->availability_flag = $record[1];
                $match->save();
            }
        }
    }
}
