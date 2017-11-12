<?php 

namespace App\Api\V1\Ico\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliates extends Model
{
    protected $table = 'ico_affiliates';
    protected $fillable = ['id', 'affiliate_code', 'account_number', 'affiliate_type'];
    protected $casts =
    [
        'id' => 'int',
    ];

}
