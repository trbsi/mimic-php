<?php

namespace App\Api\V1\Controllers\Mimic;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mimic;
use App\Models\Follow;
use App\Helpers\FileUploadHelper;
use App\Models\MimicTaguser;
use App\Models\MimicHashtag;
use App\Models\MimicResponse;
use DB;

class MimicController extends BaseAuthController
{
    public function __construct(User $user,
     Mimic $mimic, 
     MimicTaguser $mimicTaguser, 
     MimicHashtag $mimicHashtag,
     Follow $follow,
     MimicResponse $mimicResponse)
    {
        parent::__construct($user);
        $this->mimic = $mimic;
        $this->follow = $follow;
        $this->mimicTaguser = $mimicTaguser;
        $this->mimicHashtag = $mimicHashtag;
        $this->mimicResponse = $mimicResponse;
    }

    /**
     * Add new mimic
     * @param Request $request
     */
    public function addMimic(Request $request, FileUploadHelper $fileUpload)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $mime = $media->getMimeType();

            if (strpos($mime, "video") !== false) {
                $type = Mimic::TYPE_VIDEO;
            } elseif (strpos($mime, "image") !== false) {
                $type = Mimic::TYPE_PIC;
            } else {
                throw new \Exception(trans("validation.file_should_be_image_video"), 403);
            }

            //upload mimic
            //path: files/user/USER_ID/YEAR/
            $file = $fileUpload->upload($file, Mimic::FILE_PATH . $this->authUser->id . "/" . date("Y") . "/");

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

                $mimicResponse = 
                array_merge(
                    $this->mimic->getMimicResponse($this->mimic->where('id', $mimic->id)->with(['mimicResponses.responseMimic.user', 'user', 'hashtags', 'mimicTaguser'])->first()),  
                    [
                        'status' => true,
                        'showAlert' => false,
                        'message' =>
                        [
                            'title' => null,
                            'body' => null
                        ]
                    ]
                );

                DB::commit();
                return response()->json($mimicResponse);
            }

            DB::rollBack();
            return response()->json(
            [
                'status' => false,
                'showAlert' => true,
                'message' =>
                [
                    'title' => trans('core.alert.cant_upload_mimic_title'),
                    'body' => trans('core.alert.cant_upload_mimic_body'),
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
            [
                'status' => false,
                'showAlert' => true,
                'message' =>
                [
                    'title' => trans('core.alert.cant_upload_mimic_title'),
                    'body' => trans('core.alert.cant_upload_mimic_body'),
                ],
            ]);
       }

    }

    /**
     * list newest mimics
     * @param  Request $request
     */
    public function listMimics(Request $request)
    {
        $mimicsTable = $this->mimic->getTable();
        $followTable = $this->follow->getTable();

        $offset = 0;
        if($request->page) {
            $offset = Mimic::LIST_ORIGINAL_MIMIC_LIMIT*$request->page;
        }

        $mimics = $this->mimic;
        if($request->type && $request->type == "followers") {
            $mimics = $mimics
            ->join($followTable, "$followTable.following", '=', "$mimicsTable.user_id")
            ->where('followed_by', $this->authUser->id);
        } 

        $mimics = $mimics->select("$mimicsTable.*")
        ->orderBy("$mimicsTable.id", 'DESC')
        ->limit(Mimic::LIST_ORIGINAL_MIMIC_LIMIT)
        ->offset($offset)
        ->where('is_response', 0)
        ->with(['mimicResponses.responseMimic.user', 'user', 'hashtags', 'mimicTaguser'])
        ->get();    

        return response()->json(['mimics' => $this->mimic->getMimicResponse($mimics)]);
    }

    /**
     * load responses of a specific original mimic
     * @param  Request $request
     */
    public function loadResponses(Request $request)
    {
        $mimicsTable = $this->mimic->getTable();
        $mimicResponseTable = $this->mimicResponse->getTable();

        $offset = 0;
        if($request->page) {
            $offset = Mimic::LIST_RESPONSE_MIMIC_LIMIT*$request->page;
        }

        $mimicsResponses = $this->mimic->select("$mimicsTable.*")
        ->join($mimicResponseTable, "$mimicResponseTable.response_mimic_id", '=', "$mimicsTable.id")
        ->where("$mimicResponseTable.original_mimic_id", $request->original_mimic_id)
        ->orderBy("upvote", "DESC")
        ->limit(Mimic::LIST_RESPONSE_MIMIC_LIMIT)
        ->offset($offset)
        ->get();

        return response()->json(['mimics' => $mimicsResponses]);
    }

}
