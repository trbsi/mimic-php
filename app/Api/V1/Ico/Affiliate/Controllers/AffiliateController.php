<?php

namespace App\Api\V1\Ico\Affiliate\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Affiliate\Models\Affiliate;
use DB;

class AffiliateController extends Controller
{
	/**
	 * Createaffiliate code
	 * @param  Request    $request    [description]
	 * @param  Affiliates $affiliates [description]
	 */
	public function generateAffiliateCode(Request $request, Affiliate $affiliate)
	{
		DB::beginTransaction();
		try {
			$model = $affiliate->create(
			[
				'account_number' => $request->account_number, 
				'affiliate_type' => Affiliate::GUEST
			]);

			DB::commit();
			return response()->json(['affiliate' => $model->fresh()]);
		} catch(\Exception $e) {
			DB::rollBack();
			//get code for this account number
			return response()->json(['affiliate' => $affiliate->where('account_number', $request->account_number)->first()]);
		}
	}
}