<?php
namespace App\Api\V2\Mimic\Models;

use Illuminate\Database\Eloquent\Model;

class MimicHashtag extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_hashtag';
    protected $fillable = ['id', 'mimic_id', 'hashtag_id'];
    public $timestamps = false;
    protected $casts =
        [
            'id' => 'int',
            'mimic_id' => 'int',
            'hashtag_id' => 'int',
        ];

    public function hashtag()
    {
        return $this->belongsTo(\App\Api\V2\Hashtag\Models\Hashtag::class, 'hashtag_id', 'id');
    }

    public function mimic()
    {
        return $this->belongsTo(\App\Api\V2\Mimic\Models\Mimic::class, 'mimic_id', 'id');
    }
}
