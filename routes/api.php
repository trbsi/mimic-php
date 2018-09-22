<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$this->v = '2';
$api->version('v2', function (Router $api) {

    //global routes, non protected
    $api->group(['prefix' => 'push-notifications-token'], function (Router $api) {
        $api->post('send-to-everyone', [
            'as' => 'push-notifications-token/send-to-everyone', 
            'uses' => 'App\Api\V'.$this->v.'\PushNotificationsToken\Controllers\PushNotificationsTokenController@sendNotificationToEveryone'
        ]);
    });

    $api->group(['middleware' => ['api.global']], function (Router $api) {
        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->post('login', ['uses' => 'App\Api\V'.$this->v.'\Auth\Controllers\LoginController@login']);
        });

        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('set-username', ['uses' => 'App\Api\V'.$this->v.'\Auth\Controllers\LoginController@setUsernameAndEmail']);

            $api->group(['prefix' => 'bootstrap'], function (Router $api) {
                $api->post('send-feedback', ['uses' => 'App\Api\V'.$this->v.'\Bootstrap\Controllers\BootstrapController@sendFeeback']);
            });
           
            $api->group(['prefix' => 'users'], function (Router $api) {
                $api->put('last-seen', ['uses' => 'App\Api\V'.$this->v.'\User\Controllers\UserController@updateLastSeen']);
            });
           
            $api->group(['prefix' => 'mimic'], function (Router $api) {
                $api->post('upvote', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@upvote']);
                $api->post('create', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@createMimic']);
                $api->post('report', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@reportMimic']);

                $api->delete('delete', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@delete']);

                $api->get('list', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@getMimics', 'as' => 'mimic.list']);
                $api->get('load-responses', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@loadResponses']);
                $api->get('user-mimics', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@getUserMimics']);
                $api->get('{id}/upvotes', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Controllers\MimicController@upvotes']);

                $api->group(['prefix' => 'response'], function (Router $api) {
                    $api->get('{id}/upvotes', ['uses' => 'App\Api\V'.$this->v.'\Mimic\Resources\Response\Controllers\ResponseController@upvotes']);
                });
            });

            $api->group(['prefix' => 'profile'], function (Router $api) {
                $api->get('user', ['uses' => 'App\Api\V'.$this->v.'\User\Resources\Profile\Controllers\ProfileController@get', 'as' => 'profile.user']);
                $api->post('block', ['uses' => 'App\Api\V'.$this->v.'\User\Controllers\UserController@blockUser']);
                $api->post('follow', ['uses' => 'App\Api\V'.$this->v.'\Follow\Controllers\FollowController@followUser']);
                $api->get('followers', ['uses' => 'App\Api\V'.$this->v.'\Follow\Controllers\FollowController@followers']);
                $api->get('following', ['uses' => 'App\Api\V'.$this->v.'\Follow\Controllers\FollowController@following']);
            });

            $api->group(['prefix' => 'user'], function (Router $api) {
                $api->delete('/', ['uses' => 'App\Api\V'.$this->v.'\User\Controllers\UserController@delete']);
                $api->put('/', ['uses' => 'App\Api\V'.$this->v.'\User\Controllers\UserController@update']);
                $api->group(['prefix' => 'profile'], function (Router $api) {
                    $api->put('/', ['uses' => 'App\Api\V'.$this->v.'\User\Resources\Profile\Controllers\ProfileController@update']);
                });
            });

            $api->group(['prefix' => 'push-notifications-token'], function (Router $api) {
                $api->delete('delete-by-user', ['uses' => 'App\Api\V'.$this->v.'\PushNotificationsToken\Controllers\PushNotificationsTokenController@deleteByUser']);
                $api->post('save-push-token', ['uses' => 'App\Api\V'.$this->v.'\PushNotificationsToken\Controllers\PushNotificationsTokenController@saveOrUpdateToken']);
            });

            $api->group(['prefix' => 'search'], function (Router $api) {
                $api->get('/', ['uses' => 'App\Api\V'.$this->v.'\Search\Controllers\SearchController@search']);
                $api->get('top', ['uses' => 'App\Api\V'.$this->v.'\Search\Controllers\SearchController@topHashtagsAndUsers']);
            });
        });
    });
});