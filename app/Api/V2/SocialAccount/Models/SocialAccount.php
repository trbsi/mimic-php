<?php
namespace App\Api\V2\SocialAccount\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{

    /**
     * Generated
     */

    protected $table = 'social_accounts';
    protected $fillable = ['id', 'user_id', 'provider', 'provider_id'];
    protected $casts =
        [
            'id' => 'int',
            'user_id' => 'int',
            'provider_id' => 'int',
        ];

    public function user()
    {
        return $this->belongsTo(\App\Api\V2\User\Models\User::class, 'user_id', 'id');
    }
}
