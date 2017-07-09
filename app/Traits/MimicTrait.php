<?php
namespace App\Traits;

use App\Helpers\SendPushNotification;
use App\Models\Mimic;

trait MimicTrait
{
    /**
     * generate mimic response
     * @param  [type] $mimic       [Mimic model]
     * @param  [type] $hashtags    [array of hashtags in form: [hashtag id] => hashtag name ]
     * @param  [type] $taggedUsers [array of usernames in form: [user id] => username] @TODO-TagUsers (future feature and needs to be tested)
     * @param  [type] $mimicResponses [all responses of a specific origina mimic, ordered descending by upvotes]
     * @return [type]              [description]
     */
    private function generateContentForMimicResponse($mimic, $hashtags, $mimicResponses, $taggedUsers = null)
    {
        $mimic = $this->createMimicArraySructure($mimic);

        $hashTagsTmp = [];

        //it could be an array generated with  checkTags
        if (is_array($hashtags)) {
            foreach ($hashtags as $hashtag_id => $hashtag_name) {
                $hashTagsTmp[] =
                    [
                        "hashtag_id" => $hashtag_id,
                        "hashtag_name" => $hashtag_name
                    ];
            }
        } //if it's object from database
        else if (is_object($hashtags)) {
            foreach ($hashtags as $hashtag) {
                $hashTagsTmp[] =
                    [
                        "hashtag_id" => $hashtag->id,
                        "hashtag_name" => $hashtag->name,
                    ];
            }
        }

        //@TODO-TagUsers (future feature and needs to be tested)
        /*$taggedUsersTmp = [];
        //it could be an array generated with  checkTaggedUser
        if (is_array($taggedUsers)) {
            foreach ($taggedUsers as $user_id => $username) {
                $taggedUsersTmp[] =
                    [
                        "user_id" => $user_id,
                        "username" => $username,
                    ];
            }
        } //if it's object from database
        else if (is_object($taggedUsers)) {
            foreach ($taggedUsers as $taggedUser) {
                $taggedUsersTmp[] =
                    [
                        "user_id" => $taggedUser->id,
                        "username" => $taggedUser->username
                    ];
            }
        }*/

        $mimicResponsesTmp = [];
        //get all mimic responses
        foreach ($mimicResponses as $mimicResponse) {
            $mimicResponsesTmp[] = $this->createMimicArraySructure($mimicResponse);
        }

        return
            [
                'mimic' => $mimic,
                'hashtags' => $hashTagsTmp,
                //'taggedUsers' => $taggedUsersTmp, @TODO-TagUsers (future feature and needs to be tested)
                'mimicResponses' => $mimicResponsesTmp
            ];
    }

    /**
     * create and return array structure for each mimic
     * @param  $mimic [Mimic model]
     * @return [array]        [structured array]
     */
    private function createMimicArraySructure($mimic)
    {
        return
            [
                'id' => $mimic->id,
                'user' => $mimic->user->username,
                'user_id' => $mimic->user_id,
                'mimic_type' => $this->getMimicType($mimic),
                'upvote' => $mimic->upvote,
                'file' => $mimic->file,
            ];
    }

    /**
     * get mimic type: video, image
     * @param  [type] $mimic [Mimic model]
     */
    private function getMimicType($mimic)
    {

        switch ($mimic->mimic_type) {
            case Mimic::TYPE_VIDEO:
                return 'video';
                break;
            case Mimic::TYPE_PIC:
                return 'picture';
                break;
        }
    }

    /**
     * send notification to a user if someone tags him/her
     * @param  $user [User model]
     */
    private function sendMimicTagNotification($user)
    {
        $data =
            [
                'badge' => 1,
                'sound' => 'default',
                'title' => trans('core.notifications.respond_to_mimic_title'),
                'body' => trans('core.notifications.respond_to_mimic_body'),
            ];

        SendPushNotification::sendNotification($user->id, $data);
    }
}
