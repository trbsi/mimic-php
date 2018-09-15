<?php

namespace App\Api\V2\User\Resources\Profile\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $bio
 * @property User $user
 */
class Profile extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'users_profiles';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['bio'];

    /**
     * @var array
     */
    protected $casts =
    [
        'id' => 'int',
        'user_id' => 'int',
    ];
}
