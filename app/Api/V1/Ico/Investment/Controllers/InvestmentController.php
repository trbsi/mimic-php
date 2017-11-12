<?php

namespace App\Api\V1\Ico\Investment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Investment\Models\Investment;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;
use DB;

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

			$investmentModel->update($investment->calculateAffiliateCode($investmentModel));

			//return data
			DB::commit();
			return response()->json(['investment' => $investmentModel->fresh(), 'affiliate' => $affiliateInvestorModel]);
		} catch(\Exception $e) { dd($e->getMessage());
			DB::rollBack();
            abort(400, trans('core.general.smth_went_wront_body'));
		}
	} 

	/**
	 * Save transaction id
	 * @param  Request $request
	 */
	public function saveTransactionId(Request $request, Investment $investment)
	{
		$investmentModel = $investment->find($request->id);
		$investmentModel->transaction_id = $request->transaction_id;
		$investmentModel->save();
		return response()->json(['investment' => $investmentModel->fresh()]);
	}
}