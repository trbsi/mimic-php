<?php

namespace App\Api\V1\Ico\Investment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Investment\Models\Investment;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;
use DB;

class InvestmentController extends Controller
{
	public function saveInvestment(Request $request, Investment $investment, Affiliate $affiliate)
	{
		DB::beginTransaction();
		try {
			//save investment
			$model = $investment->create($request->all());

			//generate affiliate code if it doesn't exist
			if(!$affiliateResult = $affiliate->where('account_number', $request->account_number)->first()) {
				$affiliateResult = $affiliate->create(
				[
					'account_number' => $request->account_number, 
					'affiliate_type' => Affiliate::INVESTOR
				]);
			}

			//return data
			DB::commit();
			return response()->json(['investment' => $model->fresh(), 'affiliate' => $affiliateResult]);
		} catch(\Exception $e) {
			DB::rollBack();
            abort(400, trans('core.general.smth_went_wront_body'));
		}
	} 
}