<?php

namespace App\Http\Controllers\Ico;

use Validator;
use App\Api\V1\Ico\Investment\Models\Investment;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Api\V1\Ico\BountyHunter\Models\BountyHunter;
use App\Helpers\Constants;

class IcoController extends BaseController
{
    public function ico(Investment $investment)
    {
        return view("ico.ico", [
        	'investment' => $investment->getTotalInvestment(), 
        	'icoStatus' => Investment::getIcoStatus(),
            'socialAccounts' => Constants::socialAccounts(),
        ]);
    }

    public function invest(Request $request, Investment $investment)
    {
        return view("ico.invest", [
            'discount' => $investment->checkForDiscount(),
        	'affiliate_code' => $request->affiliate_code, 
        	'minInvestment' => $investment->getMinInvestment()
        ]);
    }

    public function whitePaper()
    {
         return response()->download(storage_path().'/MimicWhitePaper.pdf');
    }


    public function contribute(Request $request, BountyHunter $bountyHunter)
    {
        $thankYouMsg = $errorMsg = null;

        if($request->showadmindata) {
            if($request->approve_dissaprove) {
                $model = $bountyHunter->find($request->id);
                $model->approved = !$model->approved;
                $model->save();
                return redirect()->route('ico-contribute',['showadmindata' => true]);
            }
            
            if($request->contribution_work) {
                $model = $bountyHunter->find($request->id);
                $model->contribution_work = $request->contribution_work."\n";
                $model->save();
                return redirect()->route('ico-contribute',['showadmindata' => true]);
            }

            if($request->approve_dissaprove) {
                $model = $bountyHunter->find($request->id);
                $model->approved = !$model->approved;
                $model->save();
                return redirect()->route('ico-contribute',['showadmindata' => true]);
            }
            
            $contributors = $bountyHunter->get();
            return view('ico.contributors-admin', ['contributors' => $contributors]);
        } else {
            $contributors = $bountyHunter->where('approved', 1)->get();
        }

        $rewards = ['reward_white_paper' => BountyHunter::REWARD_WHITEPAPER, 'reward_topic_manager' => BountyHunter::REWARD_TOPIC_MANAGER, 'reward_article' => BountyHunter::REWARD_ARTCILE];

        if($request->isMethod('post')) {

           $validator = Validator::make($request->all(), [
                'contribution_type' => 'required',
                'forum' => 'required',
                'forum_nickname' => 'required',
                'email' => 'required|email',
                'ethereum_address' => 'required',
                'previous_work' => 'required',
            ]);

            if ($validator->fails()) {
                $errorMsg = [];
                foreach ($validator->errors()->toArray() as $errorMsgs) {
                    foreach ($errorMsgs as $msg) {
                        $errorMsg[] = $msg;
                    }
                }

                $errorMsg = implode("<br>", $errorMsg);
            } else {
                switch ($request->contribution_type) {
                    case 'white_paper':
                        $request['reward'] = BountyHunter::REWARD_WHITEPAPER;
                        break;
                    case 'topic_manager':
                        $request['reward'] = BountyHunter::REWARD_TOPIC_MANAGER;
                        break;
                    case 'article':
                        $request['reward'] = BountyHunter::REWARD_ARTCILE;
                        break;
                }
                $bountyHunter->create($request->all());
                $thankYouMsg = trans('ico.thank_you_for_contributing');
            }

        }

        return view("ico.contribute", [
            'bounty_rules_conditions_table' => $this->minimizeText(trans('ico.bounty_rules_conditions_table', $rewards)),
            'bounty_rules_conditions' => $this->minimizeText(trans('ico.bounty_rules_conditions')),
            'bounty_info_infographic' => $this->minimizeText(trans('ico.bounty_info_infographic')),
            'bounty_info_white_paper' => $this->minimizeText(trans('ico.bounty_info_white_paper')),
            'bounty_info_article' => $this->minimizeText(trans('ico.bounty_info_article')),
            'thankYouMsg' => $thankYouMsg,
            'errorMsg' => $errorMsg,
            'contributors' => $contributors,
            'contributionTypes' => $bountyHunter->contributionTypes(),
            'forums' => $bountyHunter->forums(),
        ]);
    }

    private function minimizeText($text)
    {
        return trim(preg_replace('/\s\s+/', ' ', $text));
    }
}
