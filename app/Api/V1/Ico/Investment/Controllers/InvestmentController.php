<?php

namespace App\Api\V1\Ico\Investment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Investment\Models\Investment;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;
use DB;
use Validator;
use Mail;

class InvestmentController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$icoStatus = Investment::getIcoStatus();
		    if($icoStatus === 'not_started') {
				abort(400, trans('ico.hasnt_started'));
			}

			if($icoStatus === 'ended') {
				abort(400, trans('ico.ico_finished'));
			}

	        //trim affiliate_code
	        if($request->has('affiliate_code')) {
	        	$request['affiliate_code'] = trim($request->affiliate_code);
	        }

		    return $next($request);
		});
		
	}

	/**
	 * Save investment
	 * @param  Request    $request    [description]
	 * @param  Investment $investment [description]
	 * @param  Affiliate  $affiliate  [description]
	 */
	public function saveInvestment(Request $request, Investment $investment, Affiliate $affiliate)
	{
		$zeroEth = false;
		if($request->nula_eth == true) {
			$zeroEth = true;
		}

		$messages = [
		    'required' => trans('ico.validation_required'),
		    'email' => trans('ico.validation_email'),
		    'numeric' => trans('ico.validation_numeric'),
		    'mimicoins_bought.min' => trans('ico.num_of_coins', ['num_of_coins' => $investment->getMinInvestment()]),
		];

	   $validator = Validator::make($request->all(), [
            'investor_account_number' => 'required',
	        'first_name' => 'required',
	        'last_name' => 'required',
	        'mimicoins_bought' => 'required|numeric|min:'.$investment->getMinInvestment(),
	        'email' => 'required|email',
        ], $messages);

        if ($validator->fails()) {
        	$errorMsg = [];
        	foreach ($validator->errors()->toArray() as $errorMsgs) {
        		foreach ($errorMsgs as $msg) {
	        		$errorMsg[] = $msg;
	        	}
        	}

        	abort(400, implode("<br>", $errorMsg));
        }


		DB::beginTransaction();
		try {
			//save investment
			$request['mimicoins_bought'] = $investment->roundNumber($request->mimicoins_bought);
			$investmentModel = $investment->create($request->all());

			//check for affiliate code user entered (this is affiliate code of another user)
			$affiliateCodeModel = NULL;
			if($request->affiliate_code) {
				if($affiliateCodeModel = $affiliate->where('affiliate_code', $request->affiliate_code)->first()) {
					$investmentModel->affiliate_id = $affiliateCodeModel->id;
					$investmentModel->save();
				}
			}

			//generate affiliate code for investor's account if it doesn't exist
			if(!$affiliateInvestorModel = $affiliate->where('account_number', $request->investor_account_number)->first()) {
				$affiliateInvestorModel = $affiliate->create(
				[
					'account_number' => $request->investor_account_number, 
					'affiliate_type' => Affiliate::INVESTOR
				]);
			}

			$investmentModel->update(
				array_only
				(
					$investment->calculateAffiliateCode($investmentModel, $zeroEth), 
					['phase', 'number_of_eth_to_pay', 'other_account_number', 'amount_to_send_to_other_account', 'amount_to_send_to_investor']
				)
			);


		    //get data from solidity and save transaction id
		    //http://codular.com/curl-with-php
		    /*$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => env('ICO_SOLIDITY_URL'),
			    CURLOPT_POST => 1,
			    CURLOPT_POSTFIELDS => $investmentModel->fresh()->toArray()
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
			*/
    		$investmentModel->transaction_id = mt_rand();
			$investmentModel->save();


			//send email to investor
			if(env('APP_ENV') !== 'local') {
				Mail::send('ico.emails.invested', 
					[
						'investmentModel' => $investmentModel, 
						'affiliateInvestorModel' => $affiliateInvestorModel,
						'affiliateUrl' => route('ico-invest', ['affiliate' => $affiliateInvestorModel->affiliate_code]),
					], function ($message) use ($investmentModel)
		        {

		            //$message->from('me@gmail.com', 'Christian Nwamba');

		            $message->to($investmentModel->email);

		            //Add a subject
		            $message->subject("Mimic ICO");

		        });
			}

			//return data
			DB::commit();
			return response()->json(['investment' => $investmentModel->fresh(), 'affiliate' => $affiliateInvestorModel]);
		} catch(\Exception $e) {
			DB::rollBack();
            abort(400, $e->getMessage());
		}
	} 

	/**
	 * Save transaction id coming from solidity
	 * @param  Request $request
	 */
	public function saveTransactionId(Request $request, Investment $investment)
	{
		$investmentModel = $investment->find($request->id);
		$investmentModel->transaction_id = $request->transaction_id;
		$investmentModel->save();
		return response()->json(['investment' => $investmentModel->fresh()]);
	}

	/**
	 * Calculate how muc coins will investor and other person get
	 * @param  Request $request
	 */
	public function calculateInvestment(Request $request, Investment $investment, Affiliate $affiliate)
	{
		if($request->mimicoins_bought < $investment->getMinInvestment()) {
			abort(403, trans('ico.num_of_coins', ['num_of_coins' => $investment->getMinInvestment()]));
		}
		$class = new \stdClass;
		$class->mimicoins_bought = $request->mimicoins_bought;
		$class->investor_account_number = $request->investor_account_number;
		$class->icoAffiliate = $affiliate->where('affiliate_code', $request->affiliate_code)->first();
		return $investment->calculateAffiliateCode($class);
	}
}