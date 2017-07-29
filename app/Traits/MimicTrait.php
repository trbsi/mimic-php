<?php
namespace App\Traits;

use App\Helpers\SendPushNotification;
use App\Models\Mimic;

trait MimicTrait
{
    /**
     * Return file attribues like dimensions
     * @param  string $file Path to a file
     * @param  string $mime Mime type of a file
     * @return array Widht and height of a file
     */
    public function getFileAttributes($file, $mime)
    {
        //image
        if(strpos($mime, 'image') !== false) {
            list($width, $height) = getimagesize($file);
            $mimic_type = Mimic::TYPE_PIC;
        }
        //video
        else {
            $getID3 = new \getID3;
            $fileAttributes = $getID3->analyze($file);
            $width = $fileAttributes['video']['resolution_x'];
            $height = $fileAttributes['video']['resolution_y'];
            $mimic_type = Mimic::TYPE_VIDEO;
        }

        return ['width' => $width, 'height' => $height];
    }

    /**
     * Get file path for a mimic
     * @param  object $user Authenticated user model or User model
     * @param  object $model Mimic model
     * @return string Path to a file or a folder of a mimic
     */
    public function getFileOrPath($user, $file = null, $model = null, $includeDomain = false)
    {
        if ($includeDomain) {
            $includeDomain = env('APP_URL');
        }

        if ($model != null) {
            $Y = date("Y", strtotime($model->created_at));
            $m = date("m", strtotime($model->created_at));
        } else {
            $Y = date("Y");
            $m = date("m");
        }
        return $includeDomain . Mimic::FILE_PATH . $user->id . "/" . $Y . "/" . $m . "/" . $file;
    }

    /**
     * Get Mimic type
     * @param  int $type 0/1
     * @return string "video/picture"
     */
    private function getMimicType($type)
    {
        switch ($type) {
            case Mimic::TYPE_VIDEO:
                return 'video';
                break;
            case Mimic::TYPE_PIC:
                return 'picture';
                break;
        }
    }

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
                'mimic_responses' => $mimicResponsesTmp
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
                'username' => $mimic->user->username,
                'profile_picture' => $mimic->user->profile_picture,
                'user_id' => $mimic->user_id,
                'mimic_type' => $mimic->mimic_type,
                'upvote' => $mimic->upvote,
                'file' => $mimic->file,
                'aws_file' => $mimic->aws_file,
                'upvoted' => $mimic->upvoted,
                'height' => $mimic->height,
                'width' => $mimic->width,
            ];
    }
}
