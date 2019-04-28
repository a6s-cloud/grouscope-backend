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
        TwitterClientService::createTweetText($id, $params, $localStorage, $tweetsFileForWordcloud);

        return response("0k",200);

        // logger(print_r('Total user num -> ' . count($total_users_map) , true));
        $aResult->user_count = count($total_users_map);
        $aResult->save();

        // wordcloudを実行

        // logger(print_r('python3 ../../a6s-cloud-batch/src/createWordCloud.py '
        //     . $localStoragePath . $tweetsFileForWordcloud
        //     . '../../RictyDiminished/RictyDiminished-Bold.ttf /var/www/result.png', true));
        $process = new Process([
            'python3',
            '../../a6s-cloud-batch/src/createWordCloud.py',
            $localStoragePath . $tweetsFileForWordcloud,
            '../../RictyDiminished/RictyDiminished-Bold.ttf',
            $publicStoragePath . $imageFileForWordcloud
        ]);
        // TODO: 出力したファイルをDB に保存する処理を追加する
        $process->run();
        if (!$process->isSuccessful()) {
            $aResult->status = 3;
            $aResult->save();

            $e = new ProcessFailedException($process);
            logger(print_r($e->getMessage(), true));
            logger(print_r($e->getTraceAsString(), true));

            return response($aResult->id, 500);
        }

        // update処理
        $aResult->status = 2;
        $aResult->favorite_count = $total_favorite;
        $aResult->tweet_count = $total_tweet;
        $aResult->favorite_count = $total_favorite;
        $aResult->retweet_count = $total_retweet;
        $aResult->image = $imageFileForWordcloud;
        $aResult->save();

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
        return response($aResult->id, 200);
    }
}
