<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
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

        //save or update
        $user = User::updateOrCreate(['email' => $request->email], $request->all());
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
                'status' => $status,
                'token' => $token,
                'message' => $message,
                'showAlert' => $showAlert,
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
