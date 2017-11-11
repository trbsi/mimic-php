<?php 
namespace App\Api\V1\Follow\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{

    /**
     * Generated
     */

    protected $table = 'follow';
    protected $fillable = ['id', 'followed_by', 'following'];
    protected $casts =
        [
            'id' => 'int',
            'followed_by' => 'int', //user who is following another user
            'following' => 'int', //user who is being followed
        ];

    public function followedBy()
    {
        return $this->belongsTo(\App\Api\V1\User\Models\User::class, 'followed_by', 'id');
    }

    public function following()
    {
        return $this->belongsTo(\App\Api\V1\User\Models\User::class, 'following', 'id');
    }


}
