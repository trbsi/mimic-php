<?php

namespace App\Api\V1\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Api\V1\User\Models\User;
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

        //try to get user
        $user = User::whereHas('socialAccounts', function ($query) use ($provider_data) {
            $query->where('provider_id', $provider_data['provider_id']);
            $query->where('provider', $provider_data['provider']);
        })->first();

        if (!$user) {
            try {
                $user = $this->user->create(array_only($provider_data, ['email', 'profile_picture']));
                $user->socialAccounts()->create(array_only($provider_data, ['provider', 'provider_id']));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                abort(400, trans('core.login.login_failed_body'));
            }
        } //update user
        else {
            $user->update(array_only($provider_data, ['email', 'profile_picture']));
            DB::commit();
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
                'user_id' => $user->id
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

        if (!preg_match('/^[a-zA-Z0-9_.-]{4,}$/', $request->username)) {
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
