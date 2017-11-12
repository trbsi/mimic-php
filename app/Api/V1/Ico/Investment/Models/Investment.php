<?php 

namespace App\Api\V1\Ico\Investment\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Helpers\Helper;

class Investment extends Model
{
    protected $table = 'ico_investments';
    protected $fillable = ['id', 'first_name', 'last_name', 'account_number', 'amount_invested', 'mimicoin_bought'];
    protected $casts =
    [
        'id' => 'int',
    ];

    /**
     * Get total investments we collected
     */
    public function getTotalInvestment()
    {
    	if(!$ethInfo = Cache::get('ethereum_price')) {
    		$ethInfo = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/"))[0];
    		Cache::add('ethereum_price', $ethInfo, 1440); //24hrs
    	}

    	$investedEth = Helper::numberFormat($this->sum('amount_invested'));
    	$investedUsd = Helper::numberFormat($investedEth * $ethInfo->price_usd);
    	$mimicoins = Helper::numberFormat($this->sum('mimicoin_bought'));

    	return compact('investedEth', 'investedUsd', 'mimicoins');

    }

}
