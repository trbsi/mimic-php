<?php

namespace App\Models;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JWTAuth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'facebook_id', 'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string $value
     * @return void
     */
    /*public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }*/

    /**
     * Get user by email
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        return User::where(['email' => $email])->first();
    }

    // somewhere in your controller
    // https://github.com/tymondesigns/jwt-auth/wiki/Authentication
    public function getAuthenticatedUser()
    {
        $token = JWTAuth::getToken();
        if (!$token)
            return false;

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

}
