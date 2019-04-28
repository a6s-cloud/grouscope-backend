<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AnalysisRequestService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'AnalysisRequestService';
    }
}
