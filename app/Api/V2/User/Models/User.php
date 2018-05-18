<?php

namespace App\Api\V2\User\Models;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JWTAuth;
use App\Helpers\Helper;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Api\V2\User\Traits\UserQueryTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, UserQueryTrait, SoftDeletes;

    protected $table = 'users';

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
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Format "followers" attribute
     * @param  integer $value "followers" attribute
     */
    public function getFollowersAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return Helper::numberFormat($value);
        }
    }

    /**
     * Format "following" attribute
     * @param  integer $value "following" attribute
     */
    public function getFollowingAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return Helper::numberFormat($value);
        }
    }

    /**
     * Format "number_of_mimics" attribute
     * @param  integer $value "number_of_mimics" attribute
     */
    public function getNumberOfMimicsAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return Helper::numberFormat($value);
        }
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

    // http://jwt-auth.readthedocs.io/en/develop/auth-guard/#userorfail
    public function getAuthenticatedUser()
    {
        $user = auth()->user();
        return $user;
    }

    /**
     * Get all users who I'm following
     */
    public function following()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'follow', 'followed_by', 'following')->withTimestamps();
    }

    /**
     * Get followers - users who are following me
     */
    public function followers()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'follow', 'following', 'followed_by')->withTimestamps();
    }

    /*

    public function users() {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'follow', 'following', 'followed_by');
    }


    public function mimics() {
        return $this->belongsToMany(\App\Api\V2\Mimic\Models\Mimic::class, 'mimic_upvote', 'user_id', 'mimic_id');
    }

    public function follows() {
        return $this->hasMany(\App\Api\V2\Follow\Models\Follow::class, 'followed_by', 'id');
    }

    public function follows() {
        return $this->hasMany(\App\Api\V2\Follow\Models\Follow::class, 'following', 'id');
    }

    public function mimicTagusers() {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicTaguser::class, 'user_id', 'id');
    }

    public function mimicUpvotes() {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicUpvote::class, 'user_id', 'id');
    }

    public function mimics() {
        return $this->hasMany(\App\Api\V2\Mimic\Models\Mimic::class, 'user_id', 'id');
    }

    public function pushNotificationsTokens() {
        return $this->hasMany(\App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken::class, 'user_id', 'id');
    }*/

    public function socialAccounts()
    {
        return $this->hasMany(\App\Api\V2\SocialAccount\Models\SocialAccount::class, 'user_id', 'id');
    }

    public function mimicTaguser()
    {
        return $this->belongsToMany(\App\Api\V2\Mimic\Models\Mimic::class, 'mimic_taguser', 'user_id', 'mimic_id');
    }

    public function blockedUsers() 
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'users_blocks_pivot', 'blocked_by', 'user_id');
    }
}
