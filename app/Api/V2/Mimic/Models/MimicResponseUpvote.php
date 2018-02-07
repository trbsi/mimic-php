<?php 
namespace App\Api\V2\Mimic\Models;

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

    public function mimicResponse()
    {
        return $this->belongsTo(\App\Api\V2\Mimic\Models\MimicResponse::class, 'mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Api\V2\User\Models\User::class, 'user_id', 'id');
    }
}
