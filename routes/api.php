<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {

    $api->group(['middleware' => ['api.global']], function(Router $api)
    {
        $api->group(['prefix' => 'auth'], function (Router $api) {
            /*$api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
            $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
            $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');*/
            $api->post('login', ['as' => 'users.login', 'uses' => 'App\Api\V1\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('save-push-token', ['uses' => 'App\Api\V1\Controllers\BootstrapController@updateNotificationToken']);

            $api->group(['prefix' => 'map'], function (Router $api) {

            });
        });        
    });


});
