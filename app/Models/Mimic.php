<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hashtag;
use App\Models\MimicHashtag;
use App\Models\User;
use App\Traits\MimicTrait;
use App\Models\Follow;
use App\Models\MimicResponse;
use App\Models\MimicUpvote;
use App\Models\MimicResponseUpvote;
use Illuminate\Support\Collection;
use App\Helpers\Constants;
use App\Helpers\SendPushNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Mimic extends Model
{
    use MimicTrait, SoftDeletes;

    const TYPE_VIDEO = 1;
    const TYPE_PIC = 2;
    const FILE_PATH = '/files/user/'; //user_id/year/month/file.mp4
    const MAX_TAG_LENGTH = 50;
    const LIST_ORIGINAL_MIMICS_LIMIT = 50;
    const LIST_RESPONSE_MIMICS_LIMIT = 20;

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'file', 'aws_file', 'mimic_type', 'is_private', 'upvote', 'user_id', 'width', 'height'];
    protected $appends = ['file_url'];
    protected $casts =
    [
        'id' => 'int',
        'mimic_type' => 'int',
        'is_private' => 'boolean',
        'upvote' => 'int',
        'user_id' => 'int',
        'upvoted' => 'int', //this is to check if user upvoted mimic or not
        'responses_count' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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
        return (int)($value == NULL ? 0 : $value);
    }

    /**
     * Get number of total mimics. This is used for pagination on phone
     * @param  Request $request
     * @return int Number of mimics
     */
    public function getMimicCount($request) 
    {
    	if ($request->user_id) {
            $count = $this->where("user_id", $request->user_id)->count();
        } //filter by hashtag
        else if ($request->hashtag_id) {
            $mimicHashtagTable = (new MimicHashtag)->getTable();
            $mimicsTable = $this->getTable();
            $count = $this
                ->join($mimicHashtagTable, "$mimicHashtagTable.mimic_id", '=', "$mimicsTable.id")
                ->where('hashtag_id', $request->hashtag_id)->count();
        } else {
            $count = $this->count();
        }

        return (int)$count;
    }

    /**
     * get all original mimics (latest or from followers) from the database, with relations
     * See this for help on how to get only X items from relation table using map()
     * https://laravel.io/forum/04-05-2014-eloquent-eager-loading-to-limit-for-each-post
     * https://stackoverflow.com/questions/31700003/laravel-4-eloquent-relationship-hasmany-limit-records
     * Recent, All Posts on System
        Following, User only Following, auto-sorted by most recent
        Popular, All Posts on System, auto-sorted by most upvotes
     * Order by recent mimics is default
     * @param  Request $request Laravel request
     * @param  Object $authUser Authenitacted user
     * @return [model]          [datafrom the database]
     */
    public function getMimics($request, $authUser)
    {
        $mimicsTable = $this->getTable();
        $mimicResponseTable = (new MimicResponse)->getTable();

        $queryData = ['offset' => 0, 'orderColumn' => "$mimicsTable.id", 'orderType' => 'DESC'];
        if ($request->page) {
            $queryData['offset'] = Mimic::LIST_ORIGINAL_MIMICS_LIMIT * $request->page;
        }

        $mimics = $this;

        //filter mimics by a specific user
        if ($request->user_id) {
            //if a visitor clicks on user's profile and then on one of his mimics, get user's mimics but put the mimic he clicked on as the first in the list
            if($request->mimic_id) {
                $mimics = $mimics->orderBy(DB::raw("$mimicsTable.id=$request->mimic_id"), 'DESC');
            }
            $mimics = $mimics->where("$mimicsTable.user_id", $request->user_id);
        } 
        //filter by hashtag
        else if ($request->hashtag_id) {
            $mimicHashtagTable = (new MimicHashtag)->getTable();
            $mimics = $mimics
                ->join($mimicHashtagTable, "$mimicHashtagTable.mimic_id", '=', "$mimicsTable.id")
                ->where('hashtag_id', $request->hashtag_id);
        }    
        //default is to get mimics from people you follow and then every other recent
        else {
            $followTable = (new Follow)->getTable();
            $mimics = $mimics
                ->leftJoin($followTable, function($join) use($followTable, $mimicsTable, $authUser) {
                    $join->on("$followTable.following", '=', "$mimicsTable.user_id");
                    $join->where('followed_by', $authUser->id);
                })
            	->orderBy(DB::raw('ISNULL(follow.following)'), 'ASC') //order by mimics from people you follow
                ;
                
        }

        $mimics = $mimics->select("$mimicsTable.*")
            ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicsTable.id), 1, 0) AS upvoted")
            ->selectRaw("(SELECT COUNT(*) FROM $mimicResponseTable WHERE original_mimic_id = $mimicsTable.id) AS responses_count")
            ->orderBy($queryData['orderColumn'], $queryData['orderType']) //then order by other recent mimics
            ->limit(Mimic::LIST_ORIGINAL_MIMICS_LIMIT)
            ->offset($queryData['offset'])
            ->with(['mimicResponses' => function ($query) use ($authUser, $mimicResponseTable) {
                $query->select("$mimicResponseTable.*");
                //check if user upvoted this mimic response
                $query->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicResponseUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = $mimicResponseTable.id), 1, 0) AS upvoted");
                //get user info for mimicResponses
                $query->with('user');
                //load responses by upvotes
                $query->orderBy("upvote", "DESC");
            }, 'user', 'hashtags', /*'mimicTagusers'*/])
            ->groupBy("$mimicsTable.id")
            ->get()
            ->map(function ($query) {
                $query->mimicResponses = $query->mimicResponses->take(Mimic::LIST_RESPONSE_MIMICS_LIMIT);
                return $query;
            });

        return $mimics;
    }

    /**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * @param  [string] $tags [list of tags: "#tag1 #tag2"]
     * @param $mimicModel - created mimic model
     * @return array
     */
    public function checkHashtags($tags, $mimicModel)
    {
        $returnHashtags = [];
        preg_match_all("(#[a-zA-Z0-9]*)", $tags, $hashtags);
        foreach ($hashtags[0] as $hashtag) {
            if (strlen($hashtag) > self::MAX_TAG_LENGTH) {
                $hashtag = substr($hashtag, 0, self::MAX_TAG_LENGTH);
            }

            $tag = Hashtag::updateOrCreate(['name' => $hashtag]);
            $tag->increment("popularity");

            $returnHashtags[$tag->id] = $hashtag;
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
        preg_match_all("(@[a-zA-Z0-9]*)", $usernames, $usernames);
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
     * get mimic model and return response
     * @param  [type] $mimics [Mimic model]
     * @param  boolean $direct If you want to access mimic structure directly without extra parameters
     * @return [array]        [generated mimic response]
     */
    public function getMimicApiResponseContent($mimics, $direct = false)
    {
        $mimicsResponseContent = [];

        //check if this is collection of items get with get() method
        if ($mimics instanceof Collection && !$mimics->isEmpty()) {
            foreach ($mimics as $mimic) {
                $mimicsResponseContent[] = $this->generateContentForMimicResponse
                (
                    $mimic,
                    ($mimic->hashtags) ? $mimic->hashtags : [],
                    ($mimic->mimicResponses) ? $mimic->mimicResponses : []
                );
            }
        } //if this is single item taken with first()
        else if($mimics instanceof Collection == false && !empty($mimics)) {

            $mimicsResponseContent[] = $this->generateContentForMimicResponse
            (
                $mimics,
                ($mimics->hashtags) ? $mimics->hashtags : [],
                ($mimics->mimicResponses) ? $mimics->mimicResponses : []
            );
        }

        return $mimicsResponseContent;
    }

    /**
     * send various notification to a user
     * @param model $model Mimic/MimicResponse model
     * @param  string $type What kind of notification to send
     */
    public function sendMimicNotification($model, $type)
    {
        $data =
            [
                'badge' => 1,
                'sound' => 'default',
            ];

        if ($type == Constants::PUSH_TYPE_NEW_RESPONSE) {
            $data['title'] = trans('core.notifications.new_response_title');
            $data['body'] = trans('core.notifications.new_response_body', ['user' => $model->user->username]);
            $user_id = $model->user_id;
        }

        SendPushNotification::sendNotification($user_id, $data);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function hashtags()
    {
        return $this->belongsToMany(\App\Models\Hashtag::class, 'mimic_hashtag', 'mimic_id', 'hashtag_id');
    }

    /*public function users() {
        return $this->belongsToMany(\App\Models\User::class, 'mimic_taguser', 'mimic_id', 'user_id');
    }

    public function mimicHashtags() {
        return $this->hasMany(\App\Models\MimicHashtag::class, 'mimic_id', 'id');
    }

    */

    public function upvotes()
    {
        return $this->hasMany(\App\Models\MimicUpvote::class, 'mimic_id', 'id');
    }

    public function userUpvotes()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mimic_upvote', 'mimic_id', 'user_id')->withTimestamps();
    }

    public function mimicResponses()
    {
        return $this->hasMany(\App\Models\MimicResponse::class, 'original_mimic_id', 'id')
            ->orderBy("upvote", "DESC");

    }

    public function mimicTagusers()
    {
        return $this->hasMany(\App\Models\MimicTaguser::class, 'mimic_id', 'id');
    }

}
