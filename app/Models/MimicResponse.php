<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mimic;

class MimicResponse extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'file', 'aws_file', 'mimic_type', 'original_mimic_id', 'upvote', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_type' => 'int',
            'upvote' => 'int',
            'user_id' => 'int',
            'original_mimic_id' => 'int'
        ];

    public function originalMimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'original_mimic_id', 'id');
    }

    public function responseMimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'response_mimic_id', 'id');
    }


}
