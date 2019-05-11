<?php
namespace App\Schedules;

use AnalysisRequestService;
use TwitterClientService;
use App\AnalysisResults;

class BatchedAnalysisRequests
{
    public function __invoke()
    {
        logger(print_r('解析依頼バッチ開始', true));

        $jobs = AnalysisResults::where('status', '=', '0')->get();
        if (count($jobs) === 0) {
            logger(print_r('バッチ処理すべき解析依頼がありません(0件)', true));
            return;
        };

        foreach ($jobs as &$job) {
            logger(print_r('解析依頼処理開始[job.id: ' . $job->id . ', analysis_word: '
                    . $job->analysis_word . ', analysis_start_date: '
                    . $job->analysis_start_date . ', analysis_end_date: ' . $job->analysis_end_date . ']', true));

            $job->status = 0;       // TODO: 1
            $job->save();

            $localStorage = AnalysisRequestService::getLocalStorage();
            $tweetsFileForWordcloud = AnalysisRequestService::getTweetsFileForWordcloud();

            $params = array(
                'start_date' => '2019-05-11',
                'analysis_word' => '#Google'
            );
            $summary = TwitterClientService::createTweetText($job->id, $params, $localStorage, $tweetsFileForWordcloud);
        }

        logger(print_r('解析依頼バッチ開始', true));
    }
}

