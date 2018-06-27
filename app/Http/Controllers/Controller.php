<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\Constants;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view("welcome", [
            'socialAccounts' => Constants::socialAccounts(),
        ]);
    }

    public function legal()
    {
        return view("public.legal.legal");
    }

    public function appStore()
    {
        return redirect(env('IOS_STORE_LINK'));
    }

    /**
     * Share mimic via web on FB, IG, Twitter...
     *
     * @param int $id Original mimic id
     * @return void
     */
    public function shareMimic($id)
    {
        $model = resolve('MimicModel');
        $relations = ['user', 'hashtags', 'responses.user'];
        $mimic = $model->getOneMimicWithRelations($id, $relations);

        if (!$mimic) {
            return redirect('/');
        }

        $socialImageUrl = ($mimic->mimic_type === $mimic::TYPE_PHOTO_STRING) ? $mimic->file_url : $mimic->video_thumb_url;

        return view("public.social.share", [
            'mimic' => $mimic,
            'social' => Constants::socialAccounts(),
            'socialImageUrl' => $socialImageUrl,
        ]);
    }
}
