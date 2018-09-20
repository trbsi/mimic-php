<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
	/**
	 * Send notification to all users
	 */
    public function sendNotificationToEveryone()
    {
        return view('admin/push-notifications');
    }
}
