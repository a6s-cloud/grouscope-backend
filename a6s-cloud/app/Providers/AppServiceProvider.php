<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AnalysisRequestService;
use App\Services\TwitterClientService;
use App\Services\ScrapingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('AnalysisRequestService', AnalysisRequestService::class);
        $this->app->bind('TwitterClientService', TwitterClientService::class);
        $this->app->bind('ScrapingService', ScrapingService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
