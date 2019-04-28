<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AnalysisRequestService;

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
