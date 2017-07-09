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
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();

            if (strpos($mime, "video") !== false) {
                $type = Mimic::TYPE_VIDEO;
            } elseif (strpos($mime, "image") !== false) {
                $type = Mimic::TYPE_PIC;
            } else {
                abort(403, trans("validation.file_should_be_image_video"));
            }

            //upload mimic
            //path: files/user/USER_ID/YEAR/
            $file = $fileUpload->upload($file, $this->mimic->getFileOrPath($this->authUser), ['image', 'video'], 'server');

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

                //@TODO-TagUsers (still in progress and needs to be tested)
                //$this->mimic->checkTaggedUser($request->usernames, $mimic);

                DB::commit();
                return response()->json(
                    [
                        'mimics' => $this->mimic->getMimicResponseContent($this->mimic->where('id', $mimic->id)->with(['user', 'hashtags', 'responsesToOriginalMimic.user'])->first())
                    ]
                );
            }

            DB::rollBack();
            abort(400, trans('core.alert.cant_upload_mimic_body'));
        } catch (\Exception $e) {
            DB::rollBack();
            abort(400, $e->getMessage());
        }

    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function listMimics(Request $request)
    {
        $mimics = $this->mimic->getMimics($request);

        return response()->json(['mimics' => $this->mimic->getMimicResponseContent($mimics)]);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $mimicsResponses = $this->mimic->getMimicResponseContent($request);

        return response()->json(['mimics' => $mimicsResponses]);
    }

}
