<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ScrapingService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'ScrapingService';
    }
}
