<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MimicResponse extends Model {

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'mimic_id'];


    public function mimic() {
        return $this->belongsTo(\App\Models\Mimic::class, 'mimic_id', 'id');
    }


}
