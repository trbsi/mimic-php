<?php

namespace App\Providers\Models\PushNotificationsToken;

use Illuminate\Support\ServiceProvider;

class PushNotificationsTokenModelProvider extends ServiceProvider
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
        $this->app->bind('PushNotificationsTokenModel', function ($app) {
            $class = 'App\Api\\'.strtoupper(env('API_VERSION')).'\PushNotificationsToken\Models\PushNotificationsToken';
            return app()->make($class);
        });
    }
}
