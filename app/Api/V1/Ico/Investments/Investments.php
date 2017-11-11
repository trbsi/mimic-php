<?php 

namespace App\Api\V1\Ico\Investments;

use Illuminate\Database\Eloquent\Model;

class Investments extends Model
{
    protected $table = 'ico_investments';
    protected $fillable = ['id', 'first_name', 'last_name', 'account_number', 'amount_invested'];
    protected $casts =
    [
        'id' => 'int',
    ];

}
