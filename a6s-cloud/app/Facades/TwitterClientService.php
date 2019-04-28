<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TwitterClientService extends Facade
{
    protected static function getFacadeAccessor() {
        return 'TwitterClientService';
    }
}
