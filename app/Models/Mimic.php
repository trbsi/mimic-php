<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mimic extends Model
{

    const TYPE_VIDEO = 1;
    const TYPE_PIC = 2;
    const FILE_PATH = 'files/user/';

    /**
     * Generated
     */

    protected $table = 'mimics';
    protected $fillable = ['id', 'media', 'mimic_type', 'is_response', 'is_private', 'upvote', 'user_id'];


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
        return $this->hasMany(\App\Models\MimicResponse::class, 'mimic_id', 'id');
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
