<?php

namespace App\Http\Controllers;

use \DateTime;
use \DateTimeZone;
use App\AnalysisResults;
use App\Tweets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use AnalysisRequestService;
use TwitterClientService;

class AnalysisRequestsController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'analysis_word' => 'required',
            'analysis_timing' => 'required'
        ]);

        // パラメータを取得
        $params = AnalysisRequestService::getRequestParameters($request);

        // AnalysisResultsテーブルにレコードを追加(解析開始ステータス)
        $id = AnalysisRequestService::saveStartParameters($params);

        $localStorage = AnalysisRequestService::getLocalStorage();
        $tweetsFileForWordcloud = AnalysisRequestService::getTweetsFileForWordcloud();
        $summary = TwitterClientService::createTweetText($id, $params, $localStorage, $tweetsFileForWordcloud);

        $process = AnalysisRequestService::runWordCloud();

        if (!$process->isSuccessful()) {
            AnalysisRequestService::saveErrorParameters();

            $e = new ProcessFailedException($process);
            logger(print_r($e->getMessage(), true));
            logger(print_r($e->getTraceAsString(), true));

            return response($id, 500);
        }

        // update処理
        AnalysisRequestService::savefinishParameters($summary);
        // $aResult->status = 2;
        // $aResult->user_count = count($total_users_map);
        // $aResult->favorite_count = $total_favorite;
        // $aResult->tweet_count = $total_tweet;
        // $aResult->favorite_count = $total_favorite;
        // $aResult->retweet_count = $total_retweet;
        // $aResult->image = $imageFileForWordcloud;
        // $aResult->save();

        // word cloudの画像を添付してツイートをする
        // ※動作確認する場合はコメントアウトを外してくだい
        // TODO:投稿文言は要検討
        // $media1 = $this->twitter_client->upload('media/upload', ['media' => $localStoragePath . $imageFileForWordcloud]);
        // $parameters = [
        //     'status' => "実装テストちゅうです！！\nテスト",
        //     'media_ids' => implode(',', [$media1->media_id_string]),
        // ];
        // $result = $this->twitter_client->post('statuses/update', $parameters);

        // IDを取得を返す
        return response($id, 200);
    }
}
