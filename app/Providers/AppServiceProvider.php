<?php

namespace App\Providers;

use App\Service\CommonFacadeService;
use App\Service\CustomReturn;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('CustomReturn', function () { return new CustomReturn(); });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
