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
        'email', 'username', 'profile_picture', 'followers', 'following', 'number_of_mimics', 'i_am_following_you'
    ];

    protected $casts =
        [
            'id' => 'int',
            'followers' => 'int', //number of followers
            'following' => 'int',  //number of user I'm following
            'number_of_mimics' => 'int',
            'i_am_following_you' => 'boolean', //when I open someone else's profile check if I (loggedin user) am following another user
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Format "followers" attribute
     * @param  integer $value "followers" attribute
     */
    public function getFollowersAttribute($value)
    {
        return $this->customNumberFormat($value);
    }

    /**
     * Format "following" attribute
     * @param  integer $value "following" attribute
     */
    public function getFollowingAttribute($value)
    {
        return $this->customNumberFormat($value);
    }

    /**
     * Format "number_of_mimics" attribute
     * @param  integer $value "number_of_mimics" attribute
     */
    public function getNumberOfMimicsAttribute($value)
    {
        return $this->customNumberFormat($value);
    }


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
                return abort(404, trans('core.user.user_not_found'));
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return abort(404, 'token_expired');

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return abort(404, 'token_invalid');

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return abort(404, 'token_absent');

        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

    /**
     * Format numbers, shorten them to K/M/B
     * @param  [type]  $n         [description]
     * @param  integer $precision [description]
     * @return [type]             [description]
     */
    private function customNumberFormat($n, $precision = 0)
    {
        if ($n < 1000) {
            // Anything less than a million
            $n_format = number_format($n);
        } else if ($n < 1000000) {
            $n_format = number_format($n / 1000, $precision) . 'K';
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        }

        return $n_format;
    }

    /**
     * Get all users who I'm following
     */
    public function following()
    {
        return $this->belongsToMany(\App\Models\User::class, 'follow', 'followed_by', 'following')->withTimestamps();
    }

    /**
     * Get followers - users who are following me
     */
    public function followers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'follow', 'following', 'followed_by')->withTimestamps();
    }

    /*

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
