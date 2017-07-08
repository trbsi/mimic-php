<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use DB;

class LoginController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(Request $request, JWTAuth $JWTAuth)
    {
        //init var
        $status = true;
        $showAlert = false;
        $token = false;
        $message = [
            'body' => trans('core.login.login_failed_title'),
            'title' => trans('core.login.login_failed_body'),
        ];

        $provider_data = Helper::getOauthProviderData($request->provider, $request->provider_data);
        DB::beginTransaction();

        //try to get user by email
        if ($user = User::where('email', $provider_data['email'])->first()) {
            //check if user already logged in with that provider
            //if user didn't already logged via this provider add it to the database
            if (!$provider = $user->socialAccounts()->where('provider', $request->provider)->first()) {

                $user->socialAccounts()->create(array_only($provider_data, ['provider', 'provider_id']));

            }
            //user already logged with this provider, check if provider_id is the right one
            else
            {
                //provider ids are different, user is try to fake somthing
                if($provider->provider_id != $provider_data['provider_id']) {
                    abort(400, trans('core.login.login_failed_body'));
                }
            }
        } //user doesn't exist, create an account
        else {
            try {
                $user = $this->user->create(array_only($provider_data, ['email']))
                    ->socialAccounts()->create(array_only($provider_data, ['provider', 'provider_id']));

            } catch (\Exception $e) {
                DB::rollBack();
                abort(400, trans('core.login.login_failed_body'));
            }
        }

        DB::commit();

        if ($user) {

            try {
                $token = $JWTAuth->fromUser($user);
                if (!$token) {
                    $status = false;
                    $showAlert = true;
                } else {
                    $message = null;
                }

            } catch (JWTException $e) {
                $status = false;
                $showAlert = true;
            }
        } else {
            $status = false;
            $showAlert = true;

        }

        return response()
            ->json([
                'username' => $user->username,
                'token' => $token,
            ]);

    }

    /**
     * Set username
     * @param Request $request
     */
    public function setUsername(Request $request)
    {

    }
}
