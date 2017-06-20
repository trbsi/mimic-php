<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hashtag;
use App\Models\User;
use App\Helpers\SendPushNotification;

class Mimic extends Model
{

    const TYPE_VIDEO = 1;
    const TYPE_PIC = 2;
    const FILE_PATH = 'files/user/';
    const MAX_TAG_LENGTH = 50;
    const LIST_MIMIC_LIMIT = 50;

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'media', 'mimic_type', 'is_response', 'is_private', 'upvote', 'user_id'];

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
            $user = Hashtag::where(['username' => substr($username, 1)])->first();

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

    /**
     * generate mimic response
     * @param  [type] $mimic       [Mimic model]
     * @param  [type] $hashtags    [array of hashtags in form: [hashtag id] => hashtag name ]
     * @param  [type] $taggedUsers [array of usernames in form: [user id] => username]
     * @param  [type] $mimicResponses [all responses of a specific origina mimic, ordered descending by upvotes]
     * @return [type]              [description]
     */
    private function generateMimicResponse($mimic, $hashtags, $taggedUsers, $mimicResponses)
    {
        $mimic = 
        [
            'id' => $mimic->id,
            'user' => $mimic->user->username,
            'user_id' => $mimic->user_id,
            'mimic_type' => $this->getMimicType($mimic),
            'upvote' => $mimic->upvote,
            'file' => $mimic->file,
        ];

        $hashTagsTmp = [];

        //it could be an array generated with  checkTags
        if(is_array($hashtags)) {
            foreach ($hashtags as $hashtag_id => $hashtag_name) {
                $hashTagsTmp[] = 
                [
                    "hashtag_id" => $hashtag_id,
                    "hashtag_name" => $hashtag_name
                ];
            }
        }
        //if it's object from database
        else if(is_object($hashtags)) {
            foreach ($hashtags as $hashtag) {
                $hashTagsTmp[] = 
                [
                    "hashtag_id" => $hashtag->id,
                    "hashtag_name" => $hashtag->name,
                ];
            }
        }
        
        $taggedUsersTmp = [];
        //it could be an array generated with  checkTaggedUser
        if(is_array($taggedUsers)) {
            foreach ($taggedUsers as $user_id => $username) {
                $taggedUsersTmp[] = 
                [
                    "user_id" => $user_id,
                    "username" => $username,
                ];
            }
        }
        //if it's object from database
        else if(is_object($taggedUsers)) {
            foreach ($taggedUsers as $taggedUser) {
                $taggedUsersTmp[] = 
                [
                    "user_id" => $taggedUser->id,
                    "username" => $taggedUser->username
                ];
            }
        }

        $mimicResponsesTmp = [];
        //get all mimic responses
        foreach ($mimicResponses as $mimicResponse) {
            $mimicResponsesTmp[] = $mimicResponse->responseMimic;
        }

        return 
        [
            'mimic' => $mimic,
            'hashtags' => $hashTagsTmp,
            'taggedUsers' => $taggedUsersTmp,
            'mimicResponses' => $mimicResponsesTmp
        ];
    }

    /**
     * get mimic type: video, image
     * @param  [type] $mimic [Mimic model]
     */
    private function getMimicType($mimic)
    {
        switch ($mimic->mimic_type) {
            case Mimic::TYPE_VIDEO:
                return 'video';
                break;
            case Mimic::TYPE_PIC:
                return 'picture';
                break;
        }
    }
    /**
     * send notification to a user if someone tags him/her
     * @param  $user [User model]
     */
    private function sendMimicTagNotification($user)
    {
        $data =
            [
                'badge' => 1,
                'sound' => 'default',
                'title' => trans('core.notifications.respond_to_mimic_title'),
                'body' => trans('core.notifications.respond_to_mimic_body'),
            ];

        SendPushNotification::sendNotification($user->id, $data);
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
