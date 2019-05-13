<?php
namespace App\Schedules;

use Carbon\Carbon;
use AnalysisRequestService;
use TwitterClientService;
use App\AnalysisResults;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BatchedAnalysisRequests
{
    public function __invoke()
    {
        logger(print_r('解析依頼バッチ開始', true));

        $today = Carbon::today()->format('Y-m-d 00:00:00');
        $jobs = AnalysisResults::where('status', '=', '0')->where('analysis_start_date', '<', $today)->get();
        if (count($jobs) === 0) {
            logger(print_r('バッチ処理すべき解析依頼がありません(0件)', true));
            return;
        };

        foreach ($jobs as &$job) {
            logger(print_r('解析依頼処理開始[job.id: ' . $job->id . ', analysis_word: '
                    . $job->analysis_word . ', analysis_start_date: '
                    . $job->analysis_start_date . ', analysis_end_date: ' . $job->analysis_end_date . ']', true));

            AnalysisRequestService::setAResult($job);

            // ステータスを実行中に変更する
            $job->status = 1;       // TODO: 1
            $job->save();

            // Tweet 保存用ファイルを定義する
            $localStorage = AnalysisRequestService::getLocalStorage();
            $tweetsFileForWordcloud = AnalysisRequestService::getTweetsFileForWordcloud();

            // Tweet 取得用のパラメータを構築する
            $start_date_for_twitter = (new Carbon($job->analysis_start_date))->format('Y-m-d');
            $params_for_twitter = array(
                'start_date' => $start_date_for_twitter,
                'analysis_word' => $job->analysis_word
            );

            $summary = TwitterClientService::createTweetText($job->id, $params_for_twitter, $localStorage, $tweetsFileForWordcloud);

            $process = AnalysisRequestService::runWordCloud();
            if (!$process->isSuccessful()) {
                logger(print_r('Failed: ' . $job->analysis_word, true));
                AnalysisRequestService::saveErrorParameters();

                $e = new ProcessFailedException($process);
                logger(print_r($e->getMessage(), true));
                logger(print_r($e->getTraceAsString(), true));

                continue;
            }
            AnalysisRequestService::savefinishParameters($summary);
        }

        logger(print_r('解析依頼バッチ終了', true));
    }
}

