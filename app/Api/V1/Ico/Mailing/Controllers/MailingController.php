<?php

namespace App\Api\V1\Ico\Mailing\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Mailing\Models\Mailing;
use DB;

class MailingController extends Controller
{	
	/**
	 * Save sbscribed member for newsletter
	 */
	public function saveSubscribedMember(Request $request, Mailing $mailing)
	{	
        DB::beginTransaction();
        //try to follow
        try {
        	$mailing->create($request->all());
            DB::commit();
            return response()->json($request->all());
        } //unfollow
        catch (\Exception $e) {
            //rollback query in "try" block
            DB::rollBack();
            abort(400);
        }
	}	
}	