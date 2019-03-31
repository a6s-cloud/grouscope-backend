<?php

namespace App\Http\Controllers;

use App\AnalysisResults;
use App\Tweets;
use Request;
use Abraham\TwitterOAuth\TwitterOAuth;

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
        $twitter_config = config('twitter');
        $this->twitter_client = new TwitterOAuth(
            $twitter_config["api_key"],
            $twitter_config["secret_key"],
            $twitter_config["access_token"],
            $twitter_config["token_secret"]
        );
        // 認証要求したユーザーの情報を返す
        // $content = $this->twitter_client->get("account/verify_credentials");
        // logger(print_r($content, true));

        // twitter serch
        $params = ['q'=> '#phperkaigi',
                   'count'=> 100,
                   'result_type'=>'recent',
                   'since'=>'2019-03-30_12:00:00_JST',
                   'until'=>'2019-03-30_23:59:59_JST',
                  ];
        $statuses = $this->twitter_client->get("search/tweets", $params);

        // $limit = $statuses->headers['x-rate-limit-remaining'];
        // logger(print_r($statuses,true));

        // logger(print_r($statuses->statuses, true));
        // analsis_resultsのデータを取得できる
        // logger(print_r($results->id, true));

        $total_retweet = 0;
        $total_favorite = 0;
        $total_tweet = 0;

        for ($i=0; $i<10; $i++) {
            logger(print_r($i,true));

            foreach($statuses->statuses as $key => $value){
                // logger($key);
                // logger(print_r($value,true));
                $tweet = new Tweets;
                $tweet->analysis_result_id = $results->id;
                $tweet->user_name = $value->user->name;
                $tweet->user_account = $value->user->screen_name;
                $tweet->text = $value->text;
                $tweet->retweet_count = $value->retweet_count;
                $tweet->favorite_count = $value->favorite_count;
                // $tweet->created_at = $value->created_at;
                $tweet->created_at = date("Y/n/d H:i:s", strtotime($value->created_at));
                // logger(print_r($value->id,true));
                // $tweet->save();

                $total_retweet = $total_retweet + $value->retweet_count;
                $total_favorite = $total_favorite + $value->favorite_count;
                $total_tweet = $total_tweet + 1;
                // ユーザ数をカウントする処理
            }

            // 次のリクエストを投げる
            $params["max_id"] = $value->id - 1;
            // logger(print_r($params, true));
            $statuses = $this->twitter_client->get("search/tweets", $params);

            // 0件なら終了
            $tweetCount = count($statuses->statuses);
            // logger(print_r($tweetCount,true));
            if($tweetCount == 0){
                break;
            }
        }

        // 集計とかいろいろ

        // update処理
        // $results->status = 2;
        $results->favorite_count = $total_favorite;
        $results->tweet_count = $total_tweet;
        $results->favorite_count = $total_favorite;
        $results->save();
    }
}
