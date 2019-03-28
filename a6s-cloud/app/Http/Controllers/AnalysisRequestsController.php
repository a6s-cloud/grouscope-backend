<?php

namespace App\Http\Controllers;

use App\AnalysisResults;
use Request;

class AnalysisRequestsController extends Controller
{
    public function create(Request $request)
    {
        // bodyからパラメータを取得
        $start_date = $request::input('start_date');
        $analysis_word = $request::input('analysis_word');
        $url = $request::input('url');
        $analysis_timing = $request::input('analysis_timing');

        // analysis_resultsにデータを保存
        $results = new AnalysisResults;
        $results->analysis_start_date = $start_date;
        $results->analysis_end_date = $start_date;
        $results->analysis_word = $analysis_word;
        // $results->url = $url; TODO:カラムを追加する必要あり
        $results->status = 1;
        $results->save();

        // twitterデータ取得

        // 集計とかいろいろ

        // update処理
        $results->status = 2;
        $results->save();
    }
}
