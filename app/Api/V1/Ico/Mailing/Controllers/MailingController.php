<?php

namespace App\Api\V1\Ico\Mailing\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Ico\Mailing\Models\Mailing;
use DB;
use Mail;

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

    /**
     * Notify all subscribers when ICO starts
     */
    public function notifySubscribers(Request $request, Mailing $mailing)
    {
        foreach ($mailing->get() as $mailingModel) {
            Mail::send('ico.emails.notify-subscribers', ['mailingModel' => $mailingModel, 'day' => $request->day], function ($message) use ($mailingModel, $request)
            {
                $message->to($mailingModel->email);

                //Add a subject
                $message->subject("Mimic ICO starts in ".$request->day." days");

            });
        }
        
        return response()->json(['status' => true]);
    }
}	