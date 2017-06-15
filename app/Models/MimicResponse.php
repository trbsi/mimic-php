<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MimicResponse extends Model
{

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'response_mimic_id', 'original_mimic_id'];


    public function originalMimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'original_mimic_id', 'id');
    }

    public function responseMimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'response_mimic_id', 'id');
    }


}
