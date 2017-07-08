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
        'email', 'username'
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

    /*public function users() {
        return $this->belongsToMany(\App\Models\User::class, 'follow', 'followed_by', 'following');
    }

    public function users() {
        return $this->belongsToMany(\App\Models\User::class, 'follow', 'following', 'followed_by');
    }


    public function mimics() {
        return $this->belongsToMany(\App\Models\Mimic::class, 'mimic_upvote', 'user_id', 'mimic_id');
    }

    public function follows() {
        return $this->hasMany(\App\Models\Follow::class, 'followed_by', 'id');
    }

    public function follows() {
        return $this->hasMany(\App\Models\Follow::class, 'following', 'id');
    }

    public function mimicTagusers() {
        return $this->hasMany(\App\Models\MimicTaguser::class, 'user_id', 'id');
    }

    public function mimicUpvotes() {
        return $this->hasMany(\App\Models\MimicUpvote::class, 'user_id', 'id');
    }

    public function mimics() {
        return $this->hasMany(\App\Models\Mimic::class, 'user_id', 'id');
    }

    public function pushNotificationsTokens() {
        return $this->hasMany(\App\Models\PushNotificationsToken::class, 'user_id', 'id');
    }*/

    public function socialAccounts()
    {
        return $this->hasMany(\App\Models\SocialAccount::class, 'user_id', 'id');
    }


    public function mimicTaguser() 
    {
        return $this->belongsToMany(\App\Models\Mimic::class, 'mimic_taguser', 'user_id', 'mimic_id');
    }
}
