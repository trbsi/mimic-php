<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mimic;

class MimicResponse extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'response_mimic_id', 'original_mimic_id'];
    public $timestamps = false;
    protected $casts =
        [
            'id' => 'int',
            'response_mimic_id' => 'int',
            'original_mimic_id' => 'int',
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
