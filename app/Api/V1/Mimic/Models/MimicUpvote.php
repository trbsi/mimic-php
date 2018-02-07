<?php
namespace App\Api\V1\Mimic\Models;

use Illuminate\Database\Eloquent\Model;

class MimicUpvote extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_upvote';
    protected $fillable = ['id', 'mimic_id', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_id' => 'int',
            'user_id' => 'int',
        ];

    public function mimic()
    {
        return $this->belongsTo(\App\Api\V1\Mimic\Models\Mimic::class, 'mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Api\V1\User\Models\User::class, 'user_id', 'id');
    }
}
