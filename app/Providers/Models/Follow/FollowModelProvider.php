<?php

namespace App\Providers\Models\Follow;

use Illuminate\Support\ServiceProvider;

class FollowModelProvider extends ServiceProvider
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
        $this->app->bind('FollowModel', function ($app) {
            $class = 'App\Api\\'.strtoupper(env('API_VERSION')).'\Follow\Models\Follow';
            return app()->make($class);
        });
    }
}
