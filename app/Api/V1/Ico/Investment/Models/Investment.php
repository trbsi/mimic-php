<?php 

namespace App\Api\V1\Ico\Investment\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Helpers\Helper;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;

class Investment extends Model
{
    const INTEGER_256 = 1000000000000000000;
    private $discounts;
    private $currentDate;

    protected $table = 'ico_investments';
    protected $fillable = [
        'id', 'first_name', 'last_name', 'investor_account_number', 'mimicoins_bought', 'phase', 'number_of_eth_to_pay', 'other_account_number', 'send_to_investor', 'amount_to_send_to_other_account', 'amount_to_send_to_investor', 'email'
    ];

    protected $casts =
    [
        'id' => 'int',
        'phase' => 'int',
        'affiliate_id' => 'int',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->discounts = [
            ['start' => date("Y-12-25 00:00:00"), 'end' => date('Y-12-25 23:59:59'), 'discount_amount' => 50, 'name' => 'Christmas'],
            ['start' => date("2018-01-01 00:00:00"), 'end' => date('2018-01-01 23:59:59'), 'discount_amount' => 50, 'name' => 'New Year'],
        ];


        $this->currentDate = date('Y-m-d');
    }

    /**
     * Get ethereum account balance
     * @param  string $account_address Ethereum account balance
     * @return object {"status":"1","message":"OK","result":"39609523809523809540000000"}
     */
    public function ethAccountBalance($account_address)
    {
        $balance = json_decode(file_get_contents("https://api.etherscan.io/api?module=account&action=balance&address=".$account_address."&tag=latest&apikey=".env('ICO_ETHERSCAN_API_KEY')));

        return $balance->result / self::INTEGER_256;
    }

    /**
     * Get contract balance
     * @return object {"status":"1","message":"OK","result":"39609523809523809540000000"}
     */
    public function ethContractBalance()
    {
        $balance = json_decode(file_get_contents("https://api.etherscan.io/api?module=stats&action=tokensupply&contractaddress=".env('ICO_CONTRACT_ADDRESS')."&apikey=".env('ICO_ETHERSCAN_API_KEY')));

        return $balance->result / self::INTEGER_256;
    }

    /**
     * Get ethereum price from API
     * @return 
     * [
            {
                "id": "ethereum", 
                "name": "Ethereum", 
                "symbol": "ETH", 
                "rank": "2", 
                "price_usd": "365.422", 
                "price_btc": "0.0450405", 
                "24h_volume_usd": "648471000.0", 
                "market_cap_usd": "35039752088.0", 
                "available_supply": "95888458.0", 
                "total_supply": "95888458.0", 
                "max_supply": null, 
                "percent_change_1h": "0.06", 
                "percent_change_24h": "-0.53", 
                "percent_change_7d": "9.76", 
                "last_updated": "1511376254"
            }
        ]
     */
    public function getEthPrice()
    {
        if(!$ethInfo = Cache::get('ethereum_price')) {
            $ethInfo = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/"))[0];
            Cache::add('ethereum_price', $ethInfo, 1440); //24hrs
        }

        return $ethInfo;
    }

    /**
     * Get total investments we collected
     * @return  array
     */
    public function getTotalInvestment()
    {
        $ethInfo = $this->getEthPrice();

        if(!$investedEth = Cache::get('invested_eth')) {
            $investedEth = $this->ethAccountBalance(env('ICO_OWNER_ADDRESS'));
            Cache::add('invested_eth', $investedEth, 3600); //1h
        }
    	
        if(!$mimicoins = Cache::get('mimicoins')) {
            $mimicoins = env('ICO_NUMBER_OF_COINS') - $this->ethContractBalance();
            Cache::add('mimicoins', $mimicoins, 3600); //1h
        }

    	$investedUsd = $investedEth * $ethInfo->price_usd;

    	return compact('investedEth', 'investedUsd', 'mimicoins');
    }

    /**
     * Calculate number of ethers investor and user with affiliate code needs to get
     * @param  Model $investmentModel Investment model
     * @param  boolean $zeroEth Is this investment going to cost zero ETH or not
     * @return [type] [description]
     */
    public function calculateAffiliateCode($investmentModel, $zeroEth = false)
    {
        //check if user wants to use his affiliate code
        /*if($investmentModel->icoAffiliate && $investmentModel->investor_account_number == $investmentModel->icoAffiliate->account_number) {
            abort(403, trans('ico.you_cant_use_your_aff_code'));
        }*/
        
        $calculateInvestmentBasedOnPhase = $this->calculateInvestmentBasedOnPhase($investmentModel);
        $amountToSendToInvestor = $investmentModel->mimicoins_bought; 
        $otherAccountNumber = $amountToSendToOtherAccount = null;

        /*if($investmentModel->icoAffiliate) {
            //refer to: https://docs.google.com/spreadsheets/d/1j1KAHTvt4xMxLtx_pgGbcxYLypbpc62-kmINC1KDeH4/edit?usp=sharing
            if($investmentModel->icoAffiliate->affiliate_type === Affiliate::GUEST) {
                //send to investor
                $amountToSendToInvestor = $investmentModel->mimicoins_bought + $investmentModel->mimicoins_bought * env('ICO_GUEST_PERCENTAGE_SEND_TO_INVESTOR') / 100;

                //send to guest (affiliate account - account who referred investor)
                $amountToSendToOtherAccount = $this->roundNumber($investmentModel->mimicoins_bought * env('ICO_GUEST_PERCENTAGE_SEND_TO_ANOTHER_ACCOUNT') / 100);

            } else if($investmentModel->icoAffiliate->affiliate_type === Affiliate::INVESTOR) {
                //send to investor
                $amountToSendToInvestor = $investmentModel->mimicoins_bought + $this->roundNumber((env('ICO_INVESTOR_PERCENTAGE_SEND_TO_INVESTOR') / 100 * $investmentModel->mimicoins_bought));

                //send to another investor
                $amountToSendToOtherAccount = $investmentModel->mimicoins_bought * env('ICO_INVESTOR_PERCENTAGE_SEND_TO_INVESTOR') / 100;
            }

            $otherAccountNumber = $investmentModel->icoAffiliate->account_number;
        }
        */
       
        $data = compact('calculateInvestmentBasedOnPhase', 'otherAccountNumber', 'amountToSendToOtherAccount', 'amountToSendToInvestor');

        //make discounts if there is any
        if($discount = $this->checkForDiscount()) {
            $data['calculateInvestmentBasedOnPhase']['numberOfMim']+= $data['calculateInvestmentBasedOnPhase']['numberOfMim'] * $discount['discount_amount'] / 100;
        }

        $data = [
            'phase' => $data['calculateInvestmentBasedOnPhase']['phase'], 
            'number_of_mim' => ($zeroEth) ? 0 : $this->roundNumber($data['calculateInvestmentBasedOnPhase']['numberOfMim']), 
            'other_account_number' => $data['otherAccountNumber'], 
            'amount_to_send_to_other_account' => $this->roundNumber($data['amountToSendToOtherAccount']),  
            'amount_to_send_to_investor' => $this->roundNumber($data['amountToSendToInvestor']), 
        ];


        //check if user has enough balance
        if($investmentModel->investor_account_number && $balance = $this->ethAccountBalance($investmentModel->investor_account_number)) {
            if($balance < $investmentModel->number_of_eth_to_pay) {
                abort(403, trans('ico.account_balance_small'));
            }
        }

        //check if our contract has enough coins
        if($balance = $this->ethContractBalance()) {
            if($balance < $data['number_of_mim']) {
                abort(403, trans('ico.no_enough_mimic_coin', ['leftMIM' => $balance]));
            }
        }

        return $data;
    }

    /**
     * Calculate how much investor and referral accounts needs to get based on ICO phase
     * @param  Model $investmentModel Investment model
     * @return number Number of ether user has to pay
     */
    private function calculateInvestmentBasedOnPhase($investmentModel)
    {
        $phase = $this->getIcoPhase();
        switch ($phase) {
            case 1:
                $icoPhaseEth = env('ICO_PHASE_1_ETH'); 
                break;
            case 2:
                $icoPhaseEth = env('ICO_PHASE_2_ETH'); 
                break;
            case 3:
                $icoPhaseEth = env('ICO_PHASE_3_ETH');
                break;

        }

        $numberOfMim = $investmentModel->number_of_eth_to_pay * env('ICO_ETH_TO_MIM');
        $numberOfMim = $numberOfMim + $numberOfMim*$icoPhaseEth/100; 

        return compact('numberOfMim', 'phase');
    }

    /**
     * Calculate when ICO ends
     */
    public static function calculateEndIcoTime()
    {
        $date = new \DateTime(env('ICO_START'));
        $date->add(new \DateInterval('P'.(env('ICO_PHASE_1')+env('ICO_PHASE_2')+env('ICO_PHASE_3')).'D')); 
        return strtotime($date->format('Y-m-d'));
    }

    /**
     * Get ico status
     * @return string return: active, ended, not_started
     */
    public static function getIcoStatus()
    {
        $start = strtotime(env('ICO_START'));
        $end = self::calculateEndIcoTime();

        if(time() >= $start && time() <= $end) {
            return 'active';
        } else if (time() < $start) {
            return 'not_started';
        } else if(time() > $end) {
            return 'ended';
        }
    }

    /**
     * Get all users who I'm following
     */
    public function icoAffiliate()
    {
        return $this->belongsTo(\App\Api\V1\Ico\Affiliate\Models\Affiliate::class, 'affiliate_id');
    }

    /**
     * Round numbe
     * @param  string|number $value Number of string-number
     * @return number        Rounded number
     */
    public function roundNumber($value)
    {
        return round($value, 5);
    }

    /**
     * Calculate min investment in MimiCoins. Min investment is 1$ worth of MimiCoins
     * @return [type] [description]
     */
    public function getMinInvestment()
    {
        //$ethInfo = $this->getEthPrice();
        //1ETH = 370$ / 370         => 0.0027 ETH = 1$
        //1ETH = 15 MIM /*0.0027    => 0.0027 ETH = 0.0405 MIM = 1$
        return $this->roundNumber(1 / env('ICO_ETH_TO_MIM'));
    }

    /**
     * Check if there's any discount and if there is, return discount data
     * @return array|false Discount data or boolean
     */
    public function checkForDiscount()
    {
        foreach ($this->discounts as $key => $value) {
            if(strtotime($this->currentDate) >= strtotime($value['start']) && strtotime($this->currentDate) <= strtotime($value['end'])) {

                /*switch ($this->getIcoPhase()) {
                    case 1:
                        $value['discount_amount'] = 25;
                        break;
                    case 2:
                        $value['discount_amount'] = 50;
                        break;
                    case 3:
                        $value['discount_amount'] = 75;
                        break;
                }*/
                return $value;
                break;
            }
        }

        return false;
    }

    /**
     * Get current phase of ICO
     * @return integer Return ICO phase
     */
    private function getIcoPhase()
    {
        $start = env('ICO_START');

        //phase 2
        $date = new \DateTime($start);
        $date->add(new \DateInterval('P'.env('ICO_PHASE_1').'D'));
        $phase2Ends = strtotime($date->format('Y-m-d'));

        //phase 3
        $date = new \DateTime($start);
        $date->add(new \DateInterval('P'.(env('ICO_PHASE_1')+env('ICO_PHASE_2')).'D')); 
        $phase3Ends = strtotime($date->format('Y-m-d'));

        //ending
        $icoEnds = self::calculateEndIcoTime();

        $start = strtotime($start);
        $currentDate = time();

        //phase 1
        if($currentDate >= $start && $currentDate < $phase2Ends) {
            return 1;
        } 
        //phase 2
        else if($currentDate >= $phase2Ends && $currentDate < $phase3Ends) {
            return 2;
        } 
        //phase 3
        else if($currentDate >= $phase3Ends && $currentDate <= $icoEnds) {
            return 3;
        }
    }

}
