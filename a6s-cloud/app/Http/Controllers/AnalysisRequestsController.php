<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use AnalysisRequestService;
use TwitterClientService;
use App\Rules\NonDuplicateAnalysisRequest;
use App\Rules\NGAnalysisWord;
use SlackFile;

class AnalysisRequestsController extends Controller
{
    public function create(Request $request)
    {
        // バリデーション処理
        $request->validate([
            'start_date' => 'required',
            'analysis_word' => ['required', new NonDuplicateAnalysisRequest($request), new NGAnalysisWord],
            'analysis_timing' => 'required'
        ]);

        // パラメータを取得
        $params = AnalysisRequestService::getRequestParameters($request);

        // AnalysisResultsテーブルにレコードを追加(解析開始ステータス)
        $id = AnalysisRequestService::saveStartParameters($params);

        // tweetを取得して、統計情報を取得
        $localStorage = AnalysisRequestService::getLocalStorage();
        $tweetsFileForWordcloud = AnalysisRequestService::getTweetsFileForWordcloud();
        $summary = TwitterClientService::createTweetText($id, $params, $localStorage, $tweetsFileForWordcloud);

        // Word Cloud処理を実行
        $process = AnalysisRequestService::runWordCloud();
        if (!$process->isSuccessful()) {
            AnalysisRequestService::saveErrorParameters();

            $e = new ProcessFailedException($process);
            logger(print_r($e->getMessage(), true));
            logger(print_r($e->getTraceAsString(), true));

            // 開発時に出力しないためにコメントアウト
            // SlackFile::upload([
            //     'filename' => 'wordCloudError.txt',
            //     'content' => $e->getMessage().$e->getTraceAsString(),
            //     'channels' => '#grouscope_applog',
            //     'initial_comment' => 'WordCloudの処理でエラーがでたよー:sob:'
            // ]);
            return response($id, 500);
        }

        // ステータスの終了
        AnalysisRequestService::savefinishParameters($summary);

        // 結果のtweetを実行
        $publicStoragePath = AnalysisRequestService::getPublicStoragePath();
        $imageFileForWordcloud = AnalysisRequestService::getImageFileForWordcloud();
        TwitterClientService::postTweet($publicStoragePath, $imageFileForWordcloud);

        // IDを取得を返す
        return response($id, 200);
    }
}
