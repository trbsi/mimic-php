<?php 

namespace App\Api\V1\Ico\Mailing\Models;

use Illuminate\Database\Eloquent\Model;

class Mailing extends Model
{
    protected $table = 'ico_mailings';
    protected $fillable = ['id', 'name', 'email'];
    protected $casts =
    [
        'id' => 'int',
    ];

}
