<?php

namespace App\Api\V1\Controllers\Mimic;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mimic;
use App\Helpers\FileUpload;
use App\Models\MimicTaguser;
use App\Models\MimicHashtag;
use App\Api\V1\Requests\Mimic\AddMimicRequest;
use DB;

class MimicController extends BaseAuthController
{
    public function __construct(User $user,
                                Mimic $mimic,
                                MimicTaguser $mimicTaguser,
                                MimicHashtag $mimicHashtag)
    {
        parent::__construct($user);
        $this->mimic = $mimic;
        $this->mimicTaguser = $mimicTaguser;
        $this->mimicHashtag = $mimicHashtag;
    }

    /**
     * Add new mimic
     * @param Request $request
     */
    public function addMimic(AddMimicRequest $request, FileUpload $fileUpload)
    {
       return;
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $mime = $media->getMimeType();

            if (strpos($mime, "video") !== false) {
                $type = Mimic::TYPE_VIDEO;
            } elseif (strpos($mime, "image") !== false) {
                $type = Mimic::TYPE_PIC;
            } else {
                abort(403, trans("validation.file_should_be_image_video"));
            }

            //upload mimic
            //path: files/user/USER_ID/YEAR/
            $file = $fileUpload->upload($file, Mimic::FILE_PATH . $this->authUser->id . "/" . date("Y") . "/", ['image', 'video'], 'simple');

            if ($mimic = $this->mimic->create(
                [
                    'file' => $file,
                    'mimic_type' => $type,
                    'is_response' => $request->is_response,
                    'user_id' => $this->authUser->id
                ])
            ) {
                //check for hashtags
                $this->mimic->checkTags($request->hashtags, $mimic);

                //tag users
                $this->mimic->checkTaggedUser($request->usernames, $mimic);

                DB::commit();
                return response()->json(
                        $this->mimic->getMimicResponse($this->mimic->find($mimic->id)->with(['mimicResponses.responseMimic.user', 'user', 'hashtags', 'mimicTaguser'])->first())
                    );
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        }

    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function listMimics(Request $request)
    {
        $mimics = $this->mimic->getMimics($request);

        return response()->json(['mimics' => $this->mimic->getMimicResponse($mimics)]);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $mimicsResponses = $this->mimic->getMimicResponses($request);

        return response()->json(['mimics' => $mimicsResponses]);
    }

}
