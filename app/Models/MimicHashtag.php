<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MimicHashtag extends Model {

    /**
     * Generated
     */

    protected $table = 'mimic_hashtag';
    protected $fillable = ['id', 'mimic_id', 'hashtag_id'];


    public function hashtag() {
        return $this->belongsTo(\App\Models\Hashtag::class, 'hashtag_id', 'id');
    }

    public function mimic() {
        return $this->belongsTo(\App\Models\Mimic::class, 'mimic_id', 'id');
    }


}
