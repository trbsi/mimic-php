<?php

namespace App\Api\V1\Ico\Investment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Investment\Models\Investment;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;
use DB;
use Validator;

class InvestmentController extends Controller
{
	/**
	 * Save investment
	 * @param  Request    $request    [description]
	 * @param  Investment $investment [description]
	 * @param  Affiliate  $affiliate  [description]
	 */
	public function saveInvestment(Request $request, Investment $investment, Affiliate $affiliate)
	{
		if(time() <= strtotime(env('ICO_PHASE_1'))) {
			return response()->json(['message' => "ICO hasn't started yet. Be patient, it will soon :)"]);
		}

		if(time() > strtotime(env('ICO_ENDS'))) {
			return response()->json(['message' => "ICO is not longer active. Sorry, you're too late :("]);
		}

		$messages = [
		    'required' => 'The ":attribute" field is required.',
		    'email' => 'The ":attribute" field should be an email.',
		    'integer' => 'The ":attribute" field should be a number.',
		    'min' => 'You should buy at least 15 MimiCoins.',
		];

	   $validator = Validator::make($request->all(), [
            'investor_account_number' => 'required',
	        'first_name' => 'required',
	        'last_name' => 'required',
	        'mimicoins_bought' => 'required|integer|min:15',
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
			$request['mimicoins_bought'] = round($request->mimicoins_bought);
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
					$investment->calculateAffiliateCode($investmentModel), 
					['phase', 'number_of_eth_to_pay', 'other_account_number', 'amount_to_send_to_other_account', 'amount_to_send_to_investor']
				)
			);

			//return data
			DB::commit();
			return response()->json(['investment' => $investmentModel->fresh(), 'affiliate' => $affiliateInvestorModel]);
		} catch(\Exception $e) {
			DB::rollBack();
            abort(400, trans('core.general.smth_went_wront_body'));
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
		$class = new \stdClass;
		$class->mimicoins_bought = $request->mimicoins_bought;
		$class->icoAffiliate = $affiliate->where('affiliate_code', $request->affiliate_code)->first();
		return $investment->calculateAffiliateCode($class);
	}
}