<?php

namespace App\Http\Controllers\Ico;

use App\Api\V1\Ico\Investment\Models\Investment;
use Illuminate\Routing\Controller as BaseController;

class IcoController extends BaseController
{
    public function ico(Investment $investment)
    {
        return view("ico.ico", ['investment' => $investment->getTotalInvestment()]);
    }

    public function invest()
    {
        return view("ico.invest");
    }
}
