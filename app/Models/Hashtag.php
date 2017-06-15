<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{

    /**
     * Generated
     */

    protected $table = 'hashtags';
    protected $fillable = ['id', 'name', 'popularity'];


    public function mimics()
    {
        return $this->belongsToMany(\App\Models\Mimic::class, 'mimic_hashtag', 'hashtag_id', 'mimic_id');
    }

    public function mimicHashtags()
    {
        return $this->hasMany(\App\Models\MimicHashtag::class, 'hashtag_id', 'id');
    }


}
