<?php

namespace App\Http\Controllers\Ico;

use App\Api\V1\Ico\Investment\Models\Investment;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class IcoController extends BaseController
{
    public function ico(Investment $investment)
    {
        return view("ico.ico", [
        	'investment' => $investment->getTotalInvestment(), 
        	'icoStatus' => Investment::getIcoStatus()
        ]);
    }

    public function invest(Request $request, Investment $investment)
    {
        return view("ico.invest", [
        	'affiliate_code' => $request->affiliate_code, 
        	'minInvestment' => $investment->getMinInvestment()
        ]);
    }
}
