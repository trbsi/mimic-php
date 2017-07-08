<?php namespace App\Models;

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
            'followed_by' => 'int',
            'following' => 'int'
        ];

    public function followedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'followed_by', 'id');
    }

    public function following()
    {
        return $this->belongsTo(\App\Models\User::class, 'following', 'id');
    }


}
