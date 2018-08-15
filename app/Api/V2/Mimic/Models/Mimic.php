<?php 
namespace App\Api\V2\Mimic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\Hashtag\Models\Hashtag;
use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\Traits\MimicTrait;
use App\Api\V2\Mimic\Traits\MimicQueryTrait;
use App\Api\V2\Mimic\Models\MimicResponse;
use Illuminate\Support\Collection;
use App\Helpers\Constants;
use App\Helpers\SendPushNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helper;

class Mimic extends Model
{
    use MimicTrait, SoftDeletes, MimicQueryTrait;

    const TYPE_VIDEO = 1;
    const TYPE_VIDEO_STRING = 'video';
    const TYPE_PHOTO = 2;
    const TYPE_PHOTO_STRING = 'picture';
    const FILE_PATH = '/files/user/'; //user_id/year/month/file.mp4
    const MAX_TAG_LENGTH = 50;
    const LIST_ORIGINAL_MIMICS_LIMIT = 30;
    const LIST_RESPONSE_MIMICS_LIMIT = 30;

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'file', 'aws_file', 'mimic_type', 'is_private', 'upvote', 'user_id', 'aws_video_thumb'];
    protected $appends = ['file_url', 'video_thumb_url'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_type' => 'int',
            'is_private' => 'boolean',
            'upvote' => 'int',
            'user_id' => 'int',
            'upvoted' => 'int', //this is to check if user upvoted mimic or not
            'responses_count' => 'int', //this comes from withCount('responses')
            'i_am_following_you' => 'boolean', //when returning the list of mimics
        ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    /**
     * Get file with full path and url
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getFileUrlAttribute($value)
    {
        return $this->getFileOrPath($this->user_id, $this->file, $this, true);
    }

    /**
     * Get video thumb with full path and url
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getVideoThumbUrlAttribute($value)
    {
        if ($this->video_thumb) {
            return $this->getFileOrPath($this->user_id, $this->video_thumb, $this, true);
        }

        return null;
    }

    /**
     * get mimic type: video, image
     * @param  Integer $value Mimic type 0 or 1
     * @return String "video/picture"
     */
    public function getMimicTypeAttribute($value)
    {
        return $this->getMimicType($value);
    }

    /**
     * Get if mimic has been upvoted by user
     * @param  Integer $value 0 or 1 or null (on upload new mimic)
     * @return 0 or 1
     */
    public function getUpvotedAttribute($value)
    {
        return (int)($value == null ? 0 : $value);
    }

    /**
     * Format upvote attribute to be nicer like: 12k, 369M...
     * @param  integer $value A number of upvotes
     * @return string
     */
    public function getUpvoteAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return Helper::numberFormat($value);
        }
    }

    /**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * @param string $tags List of tags: "#tag1 #tag2"
     * @param Mimic $mimicModel Created mimic model
     * @return array
     */
    public function saveHashtags($tags, $mimicModel)
    {
        $returnHashtags = [];
        if (preg_match_all("(#[a-zA-Z0-9]+)", $tags, $hashtags)) {
            foreach ($hashtags[0] as $hashtag) {
                //if length of string is 1 continue becuase this regex catches string even it it's only "#"
                if (strlen($hashtag) == 1) {
                    continue;
                }

                if (strlen($hashtag) > self::MAX_TAG_LENGTH) {
                    $hashtag = substr($hashtag, 0, self::MAX_TAG_LENGTH);
                }

                $tag = Hashtag::updateOrCreate(['name' => $hashtag]);
                $tag->preventMutation = true;
                $tag->increment("popularity");

                $returnHashtags[$tag->id] = $hashtag;
            }
        }
        
        if (!empty($returnHashtags)) {
            //save to mimic_hahstag table
            $mimicModel->hashtags()->attach(array_flip($returnHashtags));
        }

        return $returnHashtags;
    }

    /**
     * @TODO-TagUsers (still in progress and needs to be tested)
     * check if person tagged a user
     * @param  String $usernames List of usernames: "@user1 @user2"
     * @param  Model $mimicModel Created mimic model
     */
    public function checkTaggedUser($usernames, $mimicModel)
    {
        $returnUsers = [];
        preg_match_all("(@[a-zA-Z0-9]+)", $usernames, $usernames);
        foreach ($usernames[0] as $username) {

            //substr exlude "@"
            $user = User::where(['username' => substr($username, 1)])->first();

            if (!empty($user)) {
                //send notification
                $this->sendMimicTagNotification($user);
            }

            $returnUsers[$user->id] = $username;
        }

        if (!empty($returnUsers)) {
            //save to mimic_taguser table
            $user->mimicTaguser()->attach(array_flip($returnUsers));
        }

        return $returnUsers;
    }


    /**
     * send various notification to a user
     * @param model $model Mimic/MimicResponse model
     * @param  string $type What kind of notification to send
     * @param array $extra Array of some extra data
     */
    public function sendMimicNotification($model, $type, $extra = [])
    {
        $data =
            [
                'badge' => 1,
                'sound' => 'default',
            ];

        if ($type === Constants::PUSH_TYPE_NEW_RESPONSE) {
            $data['title'] = trans('notifications.new_response_title');
            $data['body'] = trans('notifications.new_response_body', ['user' => $extra['authUser']->username]);
            $user_id = $model->user_id;
        } elseif ($type === Constants::PUSH_TYPE_UPVOTE) {
            $data['title'] = trans('notifications.upvote_mimic_title');
            $data['body'] = trans('notifications.upvote_mimic_body', ['user' => $extra['authUser']->username]);
            $user_id = $model->user_id;
        }

        SendPushNotification::sendNotification($user_id, array_merge($data, $extra));
    }

    /**
     * Fake user if this is my account
     *
     * @param User $authUser Authenticated user
     */
    public function getUser($authUser)
    {
        if (!in_array($authUser->email, ["dario.trbovic@yahoo.com"])) {
            $user = $authUser;
        } else {
            if (env('APP_ENV') === 'live') {
                $findUser = (rand(0, 1) === 0) ? rand(1, 95) : rand(119, 225);
            } else {
                $findUser = rand(1, 95);
            }

            $user = User::find($findUser);
        }

        return $user;
    }

    public function user()
    {
        return $this->belongsTo(\App\Api\V2\User\Models\User::class, 'user_id', 'id');
    }

    public function hashtags()
    {
        return $this->belongsToMany(\App\Api\V2\Hashtag\Models\Hashtag::class, 'mimic_hashtag', 'mimic_id', 'hashtag_id');
    }

    /*public function users() {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'mimic_taguser', 'mimic_id', 'user_id');
    }

    public function mimicHashtags() {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicHashtag::class, 'mimic_id', 'id');
    }

    */

    public function upvotes()
    {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicUpvote::class, 'mimic_id', 'id');
    }

    public function userUpvotes()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'mimic_upvote', 'mimic_id', 'user_id')->withTimestamps();
    }

    public function responses()
    {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicResponse::class, 'original_mimic_id', 'id');
    }

    public function mimicTagusers()
    {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicTaguser::class, 'mimic_id', 'id');
    }

    public function meta()
    {
        return $this->hasOne('App\Api\V2\Mimic\Resource\Meta\Models\Meta', 'mimic_id', 'id');
    }
}
