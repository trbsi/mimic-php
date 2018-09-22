<?php

namespace App\Api\V2\Bootstrap\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Api\V2\Auth\Controllers\BaseAuthController;

class BootstrapController extends BaseAuthController
{
    /**
     * Send feedback from user to us
     *
     * @param Request $request
     * @return void
     */
    public function sendFeeback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
            'files.*' => 'image',
        ], [
            'body.required' => trans('general.feedback_body_missing'),
            'files.*.uploaded' => trans('validation.file_should_be_image'),
        ]);

        if ($validator->fails()) {
            $messages = "";
            foreach ($validator->errors()->all() as $msg) {
                $messages.=$msg."\n";
            }
            abort(400, $messages);
        }

        $filePath = [];
        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                $filePath[] = str_replace('public/', 'storage/', $file->store('public/feedback_files'));
            }
        }

        $fileName = '/feedbackovi.html';
        $path = public_path().$fileName;

        $contents = view('api.bootstrap.send-feedback', [
            'user' => $this->authUser,
            'body' => $request->body,
            'filePath' => $filePath,
        ])->render();

        
        file_put_contents($path, $contents.PHP_EOL, FILE_APPEND | LOCK_EX);

        return response()->json(['message' => trans('general.thanks_for_feedback')]);
    }
}
