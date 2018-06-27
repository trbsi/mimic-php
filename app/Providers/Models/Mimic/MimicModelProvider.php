<?php

namespace App\Providers\Models\Mimic;

use Illuminate\Support\ServiceProvider;

class MimicModelProvider extends ServiceProvider
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
        $this->app->bind('MimicModel', function ($app) {
            $class = 'App\Api\\'.strtoupper(env('API_VERSION')).'\Mimic\Models\Mimic';
            return new $class();
        });
    }
}
