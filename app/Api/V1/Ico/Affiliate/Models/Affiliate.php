<?php 

namespace App\Api\V1\Ico\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
	const GUEST = 'guest';
	const INVESTOR = 'investor';

    protected $table = 'ico_affiliates';
    protected $fillable = ['id', 'affiliate_code', 'account_number', 'affiliate_type'];
    protected $casts =
    [
        'id' => 'int',
    ];

	public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->affiliate_code = $model->generateAffiliateCode();
        });
    }

    /**
     * Generate affiliate code
     */
    public function generateAffiliateCode()
    {
    	$code = strtoupper(dechex(time()+mt_rand()));
    	//try to find in the database
    	if($this->where('affiliate_code', $code)->count()) {
    		$this->generateAffiliateCode();
    	} else {
    		return $code;
    	}
    }
}
