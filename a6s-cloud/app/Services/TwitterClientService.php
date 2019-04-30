<?php
namespace App\Services;

use App\Tweets;
use AnalysisRequestService;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterClientService
{
    private $twitter_client;

    // コンストラクタ
    public function __construct()
    {
        $twitter_config = config('twitter');
        $this->twitter_client = new TwitterOAuth(
            $twitter_config["api_key"],
            $twitter_config["secret_key"],
            $twitter_config["access_token"],
            $twitter_config["token_secret"]
        );
    }

    public function getSearchTweets($params)
    {
        $format_date = AnalysisRequestService::formatDate($params["start_date"]);
        $tweet_params = ['q'=> $params["analysis_word"],
                    'count'=> 100,
                    'result_type'=>'recent',
                    'since'=> $format_date["target_start_date"].'_JST',
                    'until'=> $format_date["target_end_date"].'_JST',
                  ];

        if(isset($params["max_id"])){
            $tweet_params["max_id"] = $params["max_id"];
        }

        $searchTweet = $this->twitter_client->get("search/tweets", $tweet_params);
        return $searchTweet;
    }

    public function createTweetText($id, $params, $localStorage, $tweetsFileForWordcloud)
    {
        // サマリ件数を計算
        $summary = array(
            "total_retweet" => 0,
            "total_favorite" => 0,
            "total_tweet" => 0,
            "total_users_map" => array(),
        );

        $searchTweet = $this->getSearchTweets($params);
        if(count($searchTweet->statuses) == 0){
            return;
        }

        // 暫定的に最大10回のリクエストをする(1000件取得)
        for ($i=0; $i<10; $i++) {
            foreach($searchTweet->statuses as $key => $value){
                AnalysisRequestService::saveTweetParameters($id,$value);

                // ユーザ数カウント用のキーを登録
                $summary["total_users_map"][$value->user->screen_name] = null;

                // サマリを計算
                if(substr($value->text, 0, 2) !== "RT"){
                    // RT以外のみ「いいね」、「リツート」を加算する
                    $summary["total_retweet"] = $summary["total_retweet"] + $value->retweet_count;
                    $summary["total_favorite"] = $summary["total_favorite"] + $value->favorite_count;
                }
                $summary["total_tweet"] = $summary["total_tweet"] + 1;

                // wordcloud用のテキストファイルにtweet データを保存
                $localStorage->append($tweetsFileForWordcloud, $value->text);
            }

            $params["max_id"] = $value->id - 1;

            // 次のツイートデータを取得
            $searchTweet = $this->getSearchTweets($params);

            // 0件なら終了
            if(count($searchTweet->statuses) == 0){
                break;
            }
        }

        return $summary;
    }

    public function postTweet($publicStoragePath, $imageFileForWordcloud)
    {
        // word cloudの画像を添付してツイートをする
        // ※動作確認する場合はコメントアウトを外してくだい
        // TODO:投稿文言は要検討
        // $media1 = $this->twitter_client->upload('media/upload', ['media' => $publicStoragePath . $imageFileForWordcloud]);
        // $parameters = [
        //     'status' => "つとむ、なおとリファクタリング中です！！\nテスト(池袋 いいオフィス)",
        //     'media_ids' => implode(',', [$media1->media_id_string]),
        // ];
        // $result = $this->twitter_client->post('statuses/update', $parameters);
    }
}
