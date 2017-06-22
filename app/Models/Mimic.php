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
    const FILE_PATH = 'files/user/';
    const MAX_TAG_LENGTH = 50;
    const LIST_ORIGINAL_MIMIC_LIMIT = 50;
    const LIST_RESPONSE_MIMIC_LIMIT = 2;

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'media', 'mimic_type', 'is_response', 'is_private', 'upvote', 'user_id'];

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
        if($request->page) {
            $offset = Mimic::LIST_RESPONSE_MIMIC_LIMIT*$request->page;
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
        if($request->page) {
            $offset = Mimic::LIST_ORIGINAL_MIMIC_LIMIT*$request->page;
        }

        $mimics = $this;
        if($request->type && $request->type == "followers") {
            $mimics = $mimics
            ->join($followTable, "$followTable.following", '=', "$mimicsTable.user_id")
            ->where('followed_by', $this->authUser->id);
        } 

        return $mimics->select("$mimicsTable.*")
        ->orderBy("$mimicsTable.id", 'DESC')
        ->limit(Mimic::LIST_ORIGINAL_MIMIC_LIMIT)
        ->offset($offset)
        ->where('is_response', 0)
        ->with(['mimicResponses.responseMimic.user', 'user', 'hashtags', 'mimicTaguser'])
        ->get();    

    }

    /**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * @param  [string] $tags [list of tags: "#tag1 #tag2"]
     * @param $mimicModel - created mimic model
     * @return array
     */
    public function checkTags($tags, $mimicModel)
    {
        $return = [];
        preg_match_all("(#[a-zA-Z0-9]*)", $tags, $hashtags);
        foreach ($hashtags[0] as $hashtag) {
            if (strlen($hashtag) > self::MAX_TAG_LENGTH) {
                $hashtag = substr($hashtag, 0, self::MAX_TAG_LENGTH);
            }

            $t = Hashtag::where(['name' => $hashtag])->first();

            if (empty($t)) {
                $t = new Hashtag;
                $t->name = $hashtag;
                $t->popularity = 1;
                $t->save();
            } else {
                $t->popularity = $t->popularity + 1;
                $t->update();
            }

            //save to mimic_hahstag table
            $mimicHashtag = new $this->mimicHashtag;
            $mimicHashtag->mimic_id = $mimicModel->id;
            $mimicHashtag->hashtag_id = $t->id;
            $mimicHashtag->save();

            $return[$t->id] = $hashtag;
        }

        return $return;
    }

    /**
     * check if person tagged a user
     * @param  [string] $usernames [list of usernames: "@user1 @user2"]
     * @param $mimicModel - created mimic model
     */
    public function checkTaggedUser($usernames)
    {
        $return = [];
        preg_match_all("(@[a-zA-Z0-9]*)", $usernames, $usernames);
        foreach ($usernames[0] as $username) {

            //substr exlude "@"
            $user = User::where(['username' => substr($username, 1)])->first();

            if (!empty($user)) {
                //send notification
                $this->sendMimicTagNotification($user);
                
                //save to mimic_hahstag table
                $mimicTaguser = new $this->mimicTaguser;
                $mimicTaguser->mimic_id = $mimicModel->id;
                $mimicTaguser->user_id = $user->id;
                $mimicTaguser->save();

            }

            $return[$user->id] = $username;
        }

        return $return;
    }

    /**
     * get mimic model and return response
     * @param  [type] $mimics [Mimic model]
     * @return [array]        [generated mimic response]
     */
    public function getMimicResponse($mimics)
    {
        $mimicsResponse = [];
        foreach ($mimics as $mimic) {
            $mimicsResponse[] = $this->generateMimicResponse($mimic, $mimic->hashtags, $mimic->mimicTaguser, $mimic->mimicResponses);
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

    public function mimicResponses()
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


}
