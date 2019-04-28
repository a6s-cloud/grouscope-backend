<?php

namespace App\Services;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\AnalysisResults;

class AnalysisRequestService
{
    private $aResult;
    private $uuid;
    private $localStorage;
    private $localStoragePath;
    private $publicStoragePath;

    // コンストラクタ
    public function __construct()
    {
        $this->aResult = new AnalysisResults;
        $this->$uuid = (string) str::uuid();
        $this->$localStorage = Storage::disk('local');
        $this->$localStoragePath = $localStorage->getDriver()->getAdapter()->getPathPrefix();
        $this->$publicStoragePath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
    }

    public function getRequestParameters(Request $request)
    {
        return array(
            "start_date" => $request->input('start_date'),
            "analysis_word" => $request->input('analysis_word'),
            "url" => $request->input('url'),
            "analysis_timing" => $request->input('analysis_timing'),
        );
    }

    public function formatDate(String $start_date)
    {
        // 抽出日付形式をハイフンに変換
        $carbon = new Carbon($start_date);
        return array(
            "target_start_date" => $carbon->format('Y-m-d_00:00:00'),
            "target_end_date" => $carbon->format('Y-m-d_23:59:59'),
        );
    }

    public function saveStartParameters($params)
    {
        $format_date = $this->formatDate($params["start_date"]);
        $this->aResult->analysis_start_date = $format_date["target_start_date"];
        $this->aResult->analysis_end_date = $format_date["target_end_date"];
        $this->aResult->analysis_word = $params["analysis_word"];
        $this->aResult->url = $params["url"];
        $this->aResult->status = 1;
        $this->aResult->save();
        return $this->aResult->id;
    }
}
