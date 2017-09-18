<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {

    $api->group(['middleware' => ['api.global']], function (Router $api) {

        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->post('login', ['uses' => 'App\Api\V1\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('save-push-token', ['uses' => 'App\Api\V1\Controllers\BootstrapController@updateNotificationToken']);
            $api->post('set-username', ['uses' => 'App\Api\V1\Controllers\LoginController@setUsername']);

            $api->group(['prefix' => 'mimic'], function (Router $api) {
                $api->post('add', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@addMimic']);
                $api->get('list', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@listMimics']);
                $api->get('load-responses', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@loadResponses']);
                $api->post('upvote', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@upvote']);
                $api->delete('delete', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@delete']);
                $api->get('user-mimics', ['uses' => 'App\Api\V1\Controllers\Mimic\MimicController@getUserMimics']);
            });

            $api->group(['prefix' => 'profile'], function (Router $api) {
                $api->get('user', ['uses' => 'App\Api\V1\Controllers\Profile\ProfileController@userProfile']);
                $api->post('follow', ['uses' => 'App\Api\V1\Controllers\Profile\FollowController@followUser']);
                $api->get('followers', ['uses' => 'App\Api\V1\Controllers\Profile\FollowController@followers']);
                $api->get('following', ['uses' => 'App\Api\V1\Controllers\Profile\FollowController@following']);
            });

            $api->get('search', ['uses' => 'App\Api\V1\Controllers\Search\SearchController@search']);

        });
    });


});
