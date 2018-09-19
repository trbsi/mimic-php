<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v2', function (Router $api) {
    $api->group(['middleware' => ['api.global']], function (Router $api) {

        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->post('login', ['uses' => 'App\Api\V2\Auth\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('set-username', ['uses' => 'App\Api\V2\Auth\Controllers\LoginController@setUsernameAndEmail']);

            $api->group(['prefix' => 'bootstrap'], function (Router $api) {
                $api->post('send-feedback', ['uses' => 'App\Api\V2\Bootstrap\Controllers\BootstrapController@sendFeeback']);
                $api->post('save-push-token', ['uses' => 'App\Api\V2\Bootstrap\Controllers\BootstrapController@updateNotificationToken']);
            });
           
            $api->group(['prefix' => 'users'], function (Router $api) {
                $api->put('last-seen', ['uses' => 'App\Api\V2\User\Controllers\UserController@updateLastSeen']);
            });
           
            $api->group(['prefix' => 'mimic'], function (Router $api) {
                $api->post('upvote', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@upvote']);
                $api->post('create', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@createMimic']);
                $api->post('report', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@reportMimic']);

                $api->delete('delete', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@delete']);

                $api->get('list', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@getMimics', 'as' => 'mimic.list']);
                $api->get('load-responses', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@loadResponses']);
                $api->get('user-mimics', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@getUserMimics']);
                $api->get('{id}/upvotes', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@upvotes']);
            });

            $api->group(['prefix' => 'profile'], function (Router $api) {
                $api->get('user', ['uses' => 'App\Api\V2\User\Resources\Profile\Controllers\ProfileController@get', 'as' => 'profile.user']);
                $api->post('block', ['uses' => 'App\Api\V2\User\Controllers\UserController@blockUser']);
                $api->post('follow', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@followUser']);
                $api->get('followers', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@followers']);
                $api->get('following', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@following']);
            });

            $api->group(['prefix' => 'user'], function (Router $api) {
                $api->delete('/', ['uses' => 'App\Api\V2\User\Controllers\UserController@delete']);
                $api->put('/', ['uses' => 'App\Api\V2\User\Controllers\UserController@update']);
                $api->group(['prefix' => 'profile'], function (Router $api) {
                    $api->put('/', ['uses' => 'App\Api\V2\User\Resources\Profile\Controllers\ProfileController@update']);
                });
            });

            $api->group(['prefix' => 'push-notifications-token'], function (Router $api) {
                $api->delete('delete-by-user', ['uses' => 'App\Api\V2\PushNotificationsToken\Controllers\PushNotificationsTokenController@deleteByUser']);
            });

            $api->group(['prefix' => 'search'], function (Router $api) {
                $api->get('/', ['uses' => 'App\Api\V2\Search\Controllers\SearchController@search']);
                $api->get('top', ['uses' => 'App\Api\V2\Search\Controllers\SearchController@topHashtagsAndUsers']);
            });
        });
    });
});