<?php 

namespace App\Api\V1\Ico\Investment\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Helpers\Helper;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;

class Investment extends Model
{
    protected $table = 'ico_investments';
    protected $fillable = [
        'id', 'first_name', 'last_name', 'investor_account_number', 'mimicoins_bought', 'phase', 'number_of_eth_to_pay', 'other_account_number', 'send_to_investor', 'amount_to_send_to_other_account', 'amount_to_send_to_investor'
    ];

    protected $casts =
    [
        'id' => 'int',
        'phase' => 'int',
        'affiliate_id' => 'int',
    ];

    /**
     * Get total investments we collected
     * @return  array
     */
    public function getTotalInvestment()
    {
    	if(!$ethInfo = Cache::get('ethereum_price')) {
    		$ethInfo = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/"))[0];
    		Cache::add('ethereum_price', $ethInfo, 1440); //24hrs
    	}

    	$investedEth = Helper::numberFormat($this->sum('amount_invested'));
    	$investedUsd = Helper::numberFormat($investedEth * $ethInfo->price_usd);
    	$mimicoins = Helper::numberFormat($this->sum('mimicoins_bought'));

    	return compact('investedEth', 'investedUsd', 'mimicoins');
    }

    /**
     * Calculate number of ethers investor and user with affiliate code needs to get
     * @param  Model $investmentModel Investment model
     * @return [type] [description]
     */
    public function caluculateAffiliateCode($investmentModel)
    {
        $calculateInvestmentBasedOnPhase = $this->calculateInvestmentBasedOnPhase($investmentModel);
        $otherAccountNumber  = $amountToSendToInvestor = $amountToSendToOtherAccount = null;

        if($investmentModel->icoAffiliate) {
            //refer to: https://docs.google.com/spreadsheets/d/1j1KAHTvt4xMxLtx_pgGbcxYLypbpc62-kmINC1KDeH4/edit?usp=sharing
            if($investmentModel->icoAffiliate->affiliate_type === Affiliate::GUEST) {
                //send to investor
                $amountToSendToInvestor = $investmentModel->mimicoins_bought;

                //send to guest (affiliate account - account who referred investor)
                $amountToSendToOtherAccount = round($investmentModel->mimicoins_bought * env('ICO_GUEST_PERCENTAGE') / 100);

            } else if($investmentModel->icoAffiliate->affiliate_type === Affiliate::INVESTOR) {
                //send to investor
                $amountToSendToInvestor = $investmentModel->mimicoins_bought + round((env('ICO_INVESTOR_PERCENTAGE') / 100 * $investmentModel->mimicoins_bought));

                //send to another investor
                $amountToSendToOtherAccount = $investmentModel->mimicoins_bought;
            }

            $otherAccountNumber = $investmentModel->icoAffiliate->account_number;
        }
        
        $data = compact('calculateInvestmentBasedOnPhase', 'otherAccountNumber', 'sendToInvestor', 'amountToSendToOtherAccount', 'amountToSendToInvestor');

        return [
            'phase' => $data['calculateInvestmentBasedOnPhase']['phase'], 
            'number_of_eth_to_pay' => $data['calculateInvestmentBasedOnPhase']['numberOfEthToPay'], 
            'other_account_number' => $data['otherAccountNumber'], 
            'amount_to_send_to_other_account' => $data['amountToSendToOtherAccount'],  
            'amount_to_send_to_investor' => $data['amountToSendToInvestor'], 
        ];
    }

    /**
     * Calculate how much investor and referral accounts needs to get based on ICO phase
     * @param  Model $investmentModel Investment model
     * @return number Number of ether user has to pay
     */
    private function calculateInvestmentBasedOnPhase($investmentModel)
    {
        $phase1 = strtotime(env('ICO_PHASE_1'));
        $phase2 = strtotime(env('ICO_PHASE_2'));
        $phase3 = strtotime(env('ICO_PHASE_3'));
        $icoEnds = strtotime(env('ICO_ENDS'));
        $currentDate = time();

        //phase 1
        if($currentDate >= $phase1 && $currentDate < $phase2) {
            $numberOfEthToPay = $investmentModel->mimicoins_bought * env('ICO_PHASE_1_ETH'); 
            $phase = 1;
        } 
        //phase 2
        else if($currentDate >= $phase2 && $currentDate < $phase3) {
            $numberOfEthToPay = $investmentModel->mimicoins_bought * env('ICO_PHASE_2_ETH'); 
            $phase = 2;
        } 
        //phase 3
        else if($currentDate >= $phase3 && $currentDate <= $icoEnds) {
            $numberOfEthToPay = $investmentModel->mimicoins_bought * env('ICO_PHASE_3_ETH'); 
            $phase = 3;
        }

        return compact('numberOfEthToPay', 'phase');
    }


    /**
     * Get all users who I'm following
     */
    public function icoAffiliate()
    {
        return $this->belongsTo(\App\Api\V1\Ico\Affiliate\Models\Affiliate::class, 'affiliate_id');
    }

}
