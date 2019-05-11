<?php
namespace App\Schedules;

class BatchedAnalysisRequests
{
    public function __invoke()
    {
        logger(print_r('BatchedAnalysisRequests was called!!', true));
    }
}
// $obj = new BatchedAnalysisRequests;
// $obj(5);
// var_dump(is_callable($obj));
// ?>
