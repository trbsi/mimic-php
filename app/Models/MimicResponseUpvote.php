<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MimicResponseUpvote extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_response_upvote';
    protected $fillable = ['id', 'mimic_id', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_id' => 'int',
            'user_id' => 'int',
        ];

    public function mimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


}
