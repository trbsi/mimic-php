<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {

    $api->group(['middleware' => ['api.global']], function (Router $api) {
        $api->get('heartbeat', ['uses' => 'App\Api\V1\Bootstrap\Controllers\BootstrapController@heartbeat']);

        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->post('login', ['uses' => 'App\Api\V1\Auth\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('save-push-token', ['uses' => 'App\Api\V1\Bootstrap\Controllers\BootstrapController@updateNotificationToken']);
            $api->post('set-username', ['uses' => 'App\Api\V1\Auth\Controllers\LoginController@setUsernameAndEmail']);

            $api->group(['prefix' => 'mimic'], function (Router $api) {
                $api->post('add', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@addMimic']);
                $api->post('upload-video-thumb', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@uploadVideoThumb']);
                $api->get('list', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@listMimics']);
                $api->get('load-responses', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@loadResponses']);
                $api->post('upvote', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@upvote']);
                $api->delete('delete', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@delete']);
                $api->get('user-mimics', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@getUserMimics']);
                $api->post('report', ['uses' => 'App\Api\V1\Mimic\Controllers\MimicController@reportMimic']);
            });

            $api->group(['prefix' => 'profile'], function (Router $api) {
                $api->get('user', ['uses' => 'App\Api\V1\Profile\Controllers\ProfileController@userProfile']);
                $api->post('block', ['uses' => 'App\Api\V1\Profile\Controllers\ProfileController@blockUser']);
                $api->post('follow', ['uses' => 'App\Api\V1\Follow\Controllers\FollowController@followUser']);
                $api->get('followers', ['uses' => 'App\Api\V1\Follow\Controllers\FollowController@followers']);
                $api->get('following', ['uses' => 'App\Api\V1\Follow\Controllers\FollowController@following']);
            });

            $api->get('search', ['uses' => 'App\Api\V1\Search\Controllers\SearchController@search']);
        });
    });
});

$api->version('v2', function (Router $api) {
    $api->group(['middleware' => ['api.global']], function (Router $api) {
        $api->get('heartbeat', ['uses' => 'App\Api\V2\Bootstrap\Controllers\BootstrapController@heartbeat']);

        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->post('login', ['uses' => 'App\Api\V2\Auth\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('set-username', ['uses' => 'App\Api\V2\Auth\Controllers\LoginController@setUsernameAndEmail']);

            $api->group(['prefix' => 'bootstrap'], function (Router $api) {
                $api->post('send-feedback', ['uses' => 'App\Api\V2\Bootstrap\Controllers\BootstrapController@sendFeeback']);
                $api->post('save-push-token', ['uses' => 'App\Api\V2\Bootstrap\Controllers\BootstrapController@updateNotificationToken']);
            });
           
            $api->group(['prefix' => 'mimic'], function (Router $api) {
                $api->post('create', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@createMimic']);
                $api->get('list', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@listMimics']);
                $api->get('load-responses', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@loadResponses']);
                $api->post('upvote', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@upvote']);
                $api->delete('delete', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@delete']);
                $api->get('user-mimics', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@getUserMimics']);
                $api->post('report', ['uses' => 'App\Api\V2\Mimic\Controllers\MimicController@reportMimic']);
            });

            $api->group(['prefix' => 'profile'], function (Router $api) {
                $api->get('user', ['uses' => 'App\Api\V2\Profile\Controllers\ProfileController@userProfile']);
                $api->post('block', ['uses' => 'App\Api\V2\Profile\Controllers\ProfileController@blockUser']);
                $api->post('follow', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@followUser']);
                $api->get('followers', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@followers']);
                $api->get('following', ['uses' => 'App\Api\V2\Follow\Controllers\FollowController@following']);
            });

            $api->group(['prefix' => 'search'], function (Router $api) {
                $api->get('/', ['uses' => 'App\Api\V2\Search\Controllers\SearchController@search']);
                $api->get('top', ['uses' => 'App\Api\V2\Search\Controllers\SearchController@topHashtagsAndUsers']);
            });
        });
    });
});