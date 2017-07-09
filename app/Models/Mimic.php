<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hashtag;
use App\Models\User;
use App\Traits\MimicTrait;
use App\Models\Follow;
use App\Models\MimicResponse;

class Mimic extends Model
{
    use MimicTrait;

    const TYPE_VIDEO = 1;
    const TYPE_PIC = 2;
    const FILE_PATH = '/files/user/';
    const MAX_TAG_LENGTH = 50;
    const LIST_ORIGINAL_MIMIC_LIMIT = 50;
    const LIST_RESPONSE_MIMIC_LIMIT = 2;

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'file', 'mimic_type', 'is_response', 'is_private', 'upvote', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_type' => 'int',
            'is_response' => 'boolean',
            'is_private' => 'boolean',
            'upvote' => 'int',
            'user_id' => 'int',
        ];


    /**
     * Get file path for a mimic
     * @param  object $authUser Authenticated user model
     * @param  object $model Mimic model
     * @return string Path to a file or a folder of a mimic
     */
    public function getFileOrPath($authUser, $model = null)
    {
        if($model != null) {
            $file = $model->file;
            $Y = date("Y", strtotime($model->created_at));
            $m = date("m", strtotime($model->created_at));
        } else {
            $file = null;
            $Y = date("Y");
            $m = date("m");
        }
        return Mimic::FILE_PATH . $authUser->id . "/" . $Y."/".$m."/".$file;
    }

    /**
     * get all mimic responses of a specific original mimic
     * @param  $request
     * @return data from the database
     */
    public function getMimicResponses($request)
    {
        $mimicsTable = $this->getTable();
        $mimicResponseTable = (new MimicResponse)->getTable();

        $offset = 0;
        if ($request->page) {
            $offset = Mimic::LIST_RESPONSE_MIMIC_LIMIT * $request->page;
        }

        return $this->select("$mimicsTable.*")
            ->join($mimicResponseTable, "$mimicResponseTable.response_mimic_id", '=', "$mimicsTable.id")
            ->where("$mimicResponseTable.original_mimic_id", $request->original_mimic_id)
            ->orderBy("upvote", "DESC")
            ->limit(Mimic::LIST_RESPONSE_MIMIC_LIMIT)
            ->offset($offset)
            ->get();

    }

    /**
     * get all original mimics (latest or from followers) from the database, with relations
     * @param  [type] $request [description]
     * @return [model]          [datafrom the database]
     */
    public function getMimics($request)
    {
        $mimicsTable = $this->getTable();
        $followTable = (new Follow)->getTable();

        $offset = 0;
        if ($request->page) {
            $offset = Mimic::LIST_ORIGINAL_MIMIC_LIMIT * $request->page;
        }

        $mimics = $this;
        if ($request->type && $request->type == "followers") {
            $mimics = $mimics
                ->join($followTable, "$followTable.following", '=', "$mimicsTable.user_id")
                ->where('followed_by', $this->authUser->id);
        }

        $mimics = $mimics->select("$mimicsTable.*")
            ->orderBy("$mimicsTable.id", 'DESC')
            ->limit(Mimic::LIST_ORIGINAL_MIMIC_LIMIT)
            ->offset($offset)
            ->where('is_response', 0)
            ->with(['responsesToOriginalMimic.user', 'user', 'hashtags', 'mimicTaguser'])
            ->get();

        return $mimics;
    }

    /**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * @param  [string] $tags [list of tags: "#tag1 #tag2"]
     * @param $mimicModel - created mimic model
     * @return array
     */
    public function checkTags($tags, $mimicModel)
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

        if(!empty($returnHashtags)) {
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

        if(!empty($returnUsers)) {
            //save to mimic_taguser table
            $user->mimicTaguser()->attach(array_flip($returnUsers));
        }

        return $returnUsers;
    }

    /**
     * get mimic model and return response
     * @param  [type] $mimics [Mimic model]
     * @return [array]        [generated mimic response]
     */
    public function getMimicResponseContent($mimics)
    {
        $mimicsResponse = [];
        
        //if there are more records from the database (e.g. when listing all mimics)
        if(count($mimics) > 1) {
            foreach ($mimics as $mimic) {
                $mimicsResponse[] = $this->generateContentForMimicResponse($mimic, $mimic->hashtags, $mimic->responsesToOriginalMimic);
            } 
        } 
        //if there is only one record from the database (e.g. after uploading single mimic)
        else {
            $mimicsResponse[] = $this->generateContentForMimicResponse($mimics, $mimics->hashtags, $mimics->responsesToOriginalMimic);

        }
        

        return $mimicsResponse;
    }


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function hashtags()
    {
        return $this->belongsToMany(\App\Models\Hashtag::class, 'mimic_hashtag', 'mimic_id', 'hashtag_id');
    }

    public function mimicTaguser()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mimic_taguser', 'mimic_id', 'user_id');
    }

    public function mimicUpvote()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mimic_upvote', 'mimic_id', 'user_id');
    }

    public function mimicHashtags()
    {
        return $this->hasMany(\App\Models\MimicHashtag::class, 'mimic_id', 'id');
    }

    public function mimicOriginal()
    {
        return $this->hasMany(\App\Models\MimicResponse::class, 'original_mimic_id', 'id');
    }

    public function mimicTagusers()
    {
        return $this->hasMany(\App\Models\MimicTaguser::class, 'mimic_id', 'id');
    }

    public function mimicUpvotes()
    {
        return $this->hasMany(\App\Models\MimicUpvote::class, 'mimic_id', 'id');
    }

    public function responsesToOriginalMimic()
    {
        return $this->belongsToMany(\App\Models\Mimic::class, 'mimic_response', 'original_mimic_id', 'response_mimic_id')
            ->orderBy("upvote", "DESC")
            ->limit(Mimic::LIST_RESPONSE_MIMIC_LIMIT);
    }

    public function mimicsResponseOriginal()
    {
        return $this->belongsToMany(\App\Models\Mimic::class, 'mimic_response', 'response_mimic_id', 'original_mimic_id');
    }

    public function mimicResponses()
    {
        return $this->hasMany(\App\Models\MimicResponse::class, 'response_mimic_id', 'id');
    }


}
