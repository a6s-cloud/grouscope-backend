<?php

namespace App\Http\Controllers;

use App\AnalysisResults;
use App\Tweets;
use Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnalysisRequestsController extends Controller
{
    public function create(Request $request)
    {
        // パラメータを取得
        $start_date = $request::input('start_date');
        $analysis_word = $request::input('analysis_word');
        $url = $request::input('url');
        $analysis_timing = $request::input('analysis_timing');

        // analysis_resultsにデータを保存
        $aResult = new AnalysisResults;
        $aResult->analysis_start_date = $start_date;
        $aResult->analysis_end_date = $start_date;
        $aResult->analysis_word = $analysis_word;
        // $aResult->url = $url; TODO:カラムを追加する必要あり
        $aResult->status = 1;
        $aResult->save();

        // wordcloud 解析用のファイルpath を作成する
        // TODO: uuid をつかって一意なファイル名を作成するようにしているが、念の為そのファイルが既に作成されていないかチェックすべき
        // TODO: a6s-cloud-batch 処理成功後にこのファイルを削除する処理を追加すべき
        $tweetsFileForWordcloud = (string) str::uuid() . ".txt";
        $localStorage = Storage::disk('local');
        $localStoragePath = $localStorage->getDriver()->getAdapter()->getPathPrefix();
        // logger(print_r('次のファイルにwordcloud用tweet データを保存します[' . $localStoragePath . $tweetsFileForWordcloud . ']', true));

        // twitterデータ取得
        $twitter_config = config('twitter');
        $this->twitter_client = new TwitterOAuth(
            $twitter_config["api_key"],
            $twitter_config["secret_key"],
            $twitter_config["access_token"],
            $twitter_config["token_secret"]
        );

        // twitter serch
        $params = ['q'=> $analysis_word,
                   'count'=> 100,
                   'result_type'=>'recent',
                   'since'=>'2019-03-30_12:00:00_JST',
                   'until'=>'2019-03-30_23:59:59_JST',
                  ];
        $searchTweet = $this->twitter_client->get("search/tweets", $params);
        // ツイートデータを確認
        // logger(print_r($searchTweet->statuses, true));

        // サマリ件数を計算
        $total_retweet = 0;
        $total_favorite = 0;
        $total_tweet = 0;

        // 暫定的に最大10回のリクエストをする(1000件取得)
        for ($i=0; $i<10; $i++) {
            foreach($searchTweet->statuses as $key => $value){
                $tweet = new Tweets;
                $tweet->analysis_result_id = $aResult->id;
                $tweet->user_name = $value->user->name;
                $tweet->user_account = $value->user->screen_name;
                $tweet->text = $value->text;
                $tweet->retweet_count = $value->retweet_count;
                $tweet->favorite_count = $value->favorite_count;
                // $tweet->created_at = $value->created_at;
                $tweet->created_at = date("Y/n/d H:i:s", strtotime($value->created_at));
                $tweet->save();

                // サマリを計算
                $total_retweet = $total_retweet + $value->retweet_count;
                $total_favorite = $total_favorite + $value->favorite_count;
                $total_tweet = $total_tweet + 1;
                // ユーザ数をカウントする処理を追加する

                // wordcloud用のテキストファイルにtweet データを保存
                // TODO: a6s-cloud-batch に引数として`$localStoragePath . $tweetsFileForWordcloud` を渡して
                //       tweet データが保存されているファイルを教えてあげる必要がある
                $storage->append($tweetsFileForWordcloud, $value->text);
            }

            // 次のリクエストを投げるためのpramerをセット
            $params["max_id"] = $value->id - 1;

            // 次のツイートデータを取得
            $searchTweet = $this->twitter_client->get("search/tweets", $params);

            // 0件なら終了
            $tweetCount = count($searchTweet->statuses);
            if($tweetCount == 0){
                break;
            }
        }

        // wordcloudを実行

        // update処理
        $aResult->status = 2;
        $aResult->favorite_count = $total_favorite;
        $aResult->tweet_count = $total_tweet;
        $aResult->favorite_count = $total_favorite;
        $aResult->save();

        // IDを取得を返す
        return response($aResult->id, 200);
    }
}
