<?php

namespace App\Providers\Models\Mimic;

use Illuminate\Support\ServiceProvider;

class MimicResponseModelProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ResponseModel', function ($app) {
            $class = 'App\Api\\'.strtoupper(env('API_VERSION')).'\Mimic\Resources\Response\Models\Response';
            return app()->make($class);
        });
    }
}
