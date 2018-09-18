<?php

namespace App\Api\V2\Bootstrap\Controllers;

use Illuminate\Http\Request;
use App\Models\CoreUser as User;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use Validator;
use App\Api\V2\Auth\Controllers\BaseAuthController;

class BootstrapController extends BaseAuthController
{
    /**
     * @param Request $request
     */
    public function updateNotificationToken(Request $request)
    {
        if (isset($request->push_token) && !empty($request->push_token) && isset($request->device_id) && isset($request->device)) {
            $user = $this->user->getAuthenticatedUser();

            $PNT = PushNotificationsToken::where(['user_id' => $user->id, 'device' => $request->device, 'device_id' => $request->device_id])
                ->first();

            //you cannot find anything in database, so save it
            if (empty($PNT)) {
                $PNT = new PushNotificationsToken;
                $PNT->user_id = $user->id;
                $PNT->device_id = $request->device_id;
                $PNT->token = $request->push_token;
                $PNT->device = strtolower($request->device);
                $PNT->save();
            } //there is something in database
            else {
                $PNT->token = $request->push_token;
                $PNT->update();
            }

            return response()->json(['success' => true]);
        }

        abort(400, trans('core.push_token.parameters_not_set'));
    }

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
