<?php

namespace App\Providers;

use App\Services\LightSpeedService;
use App\Services\TckService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TckService::class, function(): TckService {
            return (new TckService(config('services.tck.key'), new LightSpeedService(config('services.ls.cluster'), config('services.ls.key'), config('services.ls.secret'), config('services.ls.language'))));
        });
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
