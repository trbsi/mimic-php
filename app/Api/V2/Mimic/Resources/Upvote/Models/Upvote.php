<?php
namespace App\Api\V2\Mimic\Resources\Upvote\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\User\Models\User;

class Upvote extends Model
{
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
        return $this->belongsTo(Mimic::class, 'mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
