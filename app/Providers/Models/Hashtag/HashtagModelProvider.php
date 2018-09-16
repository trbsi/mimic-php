<?php

namespace App\Providers\Models\Hashtag;

use Illuminate\Support\ServiceProvider;

class HashtagModelProvider extends ServiceProvider
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
        $this->app->bind('HashtagModel', function ($app) {
            $class = 'App\Api\\'.strtoupper(env('API_VERSION')).'\Hashtag\Models\Hashtag';
            return app()->make($class);
        });
    }
}
