<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Api\V1\Ico\Investment\Models\Investment;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function ajaxTest()
    {
        return view("ajax-test");
    }

    public function index()
    {
        return view("welcome", ['icoStatus' => Investment::getIcoStatus()]);
    }

    public function legal()
    {
        return view("public.legal.legal");
    }

}
