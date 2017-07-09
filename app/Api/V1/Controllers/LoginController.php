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
        $token = false;

        $provider_data = Helper::getOauthProviderData($request->provider, $request->provider_data);
        DB::beginTransaction();

        //try to get user by email
        if ($user = User::where('email', $provider_data['email'])->first()) {
            //check if user already logged in with that provider
            //if user haven't already logged via this provider add it to the database (e.g. user logged in via FB but not via twitter)
            if (!$provider = $user->socialAccounts()->where('provider', $request->provider)->first()) {

                $user->socialAccounts()->create(array_only($provider_data, ['provider', 'provider_id']));
                DB::commit();
            } //user already logged with this provider, check if provider_id is the right one
            else {
                //provider ids are different, user is trying to fake something
                if ($provider->provider_id != $provider_data['provider_id']) {
                    abort(400, trans('core.login.login_failed_body'));
                }
            }
        } //user doesn't exist, create an account
        else {
            try {
                $user = $this->user->create(array_only($provider_data, ['email']))
                    ->socialAccounts()->create(array_only($provider_data, ['provider', 'provider_id']));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                abort(400, trans('core.login.login_failed_body'));
            }
        }


        if ($user) {
            try {
                $token = $JWTAuth->fromUser($user);
                if (!$token) {
                    abort(400, trans('core.general.smth_went_wront_body'));
                }

            } catch (JWTException $e) {
                abort(400, trans('core.general.smth_went_wront_body'));
            }
        } else {
            abort(400, trans('core.general.smth_went_wront_body'));
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
        //check if username exists
        if (empty($request->username)) {
            abort(403, trans('core.login.username_empty'));
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]*$/', $request->username)) {
            abort(403, trans('core.login.username_contain'));
        }

        //username doesn't exist, create it
        if (!$this->user->where('username', $request->username)->first()) {
            $this->authUser = $this->user->getAuthenticatedUser();
            $this->authUser->update(['username' => $request->username]);

            return response()
                ->json([
                    'status' => true,
                ]);

        } else {
            abort(403, trans('core.login.username_exists'));
        }
    }
}
