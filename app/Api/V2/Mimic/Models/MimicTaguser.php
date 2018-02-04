<?php
namespace App\Api\V2\Mimic\Models;

use Illuminate\Database\Eloquent\Model;

class MimicTaguser extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_taguser';
    protected $fillable = ['id', 'mimic_id', 'user_id'];
    public $timestamps = false;
    protected $casts =
        [
            'id' => 'int',
            'mimic_id' => 'int',
            'user_id' => 'int',
        ];

    public function mimic()
    {
        return $this->belongsTo(\App\Api\V2\Mimic\Models\Mimic::class, 'mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Api\V2\User\Models\User::class, 'user_id', 'id');
    }


}
