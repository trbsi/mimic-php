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
use App\Api\V2\Follow\Models\Follow;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use App\Api\V2\User\Resources\Profile\Models\Profile;
use App\Helpers\Constants;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, UserQueryTrait, SoftDeletes;

    public const USERNAME_REGEX = '/^[a-zA-Z0-9_.-]{4,20}$/';
    
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'username', 'profile_picture'
    ];

    protected $casts =
    [
        'id' => 'int',
        'followers' => 'int', //number of followers
        'following' => 'int',  //number of user I'm following
        'number_of_mimics' => 'int',
        'i_am_following_you' => 'boolean', //when I open someone else's profile check if I (loggedin user) am following another user
        'is_blocked' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at', 'last_seen'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

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

    /**
     * @see http://jwt-auth.readthedocs.io/en/develop/auth-guard/#userorfail
     * @return User|null
     */
    public function getAuthenticatedUser()
    {
        try {
            return auth()->userOrFail();
        } catch (UserNotDefinedException $e) {
            return null;
        }
    }

    /**
     * Get part of query for i_am_following_you property
     *
     * @param object $authUser Authenticated user
     * @return string
     */
    public function getIAmFollowingYouQuery(object $authUser): string
    {
        return "IF(EXISTS(SELECT id FROM " . (new Follow)->getTable() . " WHERE followed_by = " . $authUser->id . " AND following = ".$this->getTable().".id),1,0) AS i_am_following_you";
    }

    /**
     * Get part of query for is_blocked property
     *
     * @param object $authUser Authenticated user
     * @return string
     */
    public function getIsBlockedQuery(object $authUser): string
    {
        return "IF(EXISTS(SELECT id FROM ".db_table('user_block_pivot')." WHERE blocked_by = ".$authUser->id." AND user_id = ".$this->getTable().".id),1,0) AS is_blocked";
    }

    /**
     * Get all users who I'm following
     */
    public function following()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, db_table('follow'), 'followed_by', 'following')->withTimestamps();
    }

    /**
     * Get followers - users who are following me
     */
    public function followers()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, db_table('follow'), 'following', 'followed_by')->withTimestamps();
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function socialAccounts()
    {
        return $this->hasMany(\App\Api\V2\SocialAccount\Models\SocialAccount::class, 'user_id', 'id');
    }

    public function mimicTaguser()
    {
        return $this->belongsToMany(\App\Api\V2\Mimic\Models\Mimic::class, db_table('mimic_taguser'), 'user_id', 'mimic_id');
    }

    /**
     * Get all users logged in user blocked
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, db_table('user_block_pivot'), 'blocked_by', 'user_id');
    }

    /**
     * Get all users who blocked logged in user
     */
    public function blockedFrom()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, db_table('user_block_pivot'), 'user_id', 'blocked_by');
    }
}
