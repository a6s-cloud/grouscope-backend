<?php
namespace App\Services;

class ScrapingService
{
    // evetnt_idはconnpassのURLで表示されるIDのこと
    // https://web-engineer-meetup.connpass.com/event/128855/ → 128855
    public function getConnpassData($event_id)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://connpass.com/api/v1/event/', [
            'query' => ['event_id' => $event_id]
        ]);

        $json = json_decode($res->getBody(), true);

        return array(
            "owner_display_name" => $json['events'][0]["owner_display_name"], // 管理者の表示名
            "hash_tag" => $json['events'][0]["hash_tag"], // Twitterのハッシュタグ
            "title" => $json['events'][0]["title"], // タイトル
            "waiting" => $json['events'][0]["waiting"], // 補欠者数
            "limit" => $json['events'][0]["limit"], // 定員
            "accepted" => $json['events'][0]["accepted"], // 参加者数
            "catch" => $json['events'][0]["catch"], // キャッチ
            "place" => $json['events'][0]["place"], // 開催会場
            "address" => $json['events'][0]["address"], // 開催場所
            "started_at" => $json['events'][0]["started_at"], // イベント開催日時 (ISO-8601形式)
        );
    }
}
