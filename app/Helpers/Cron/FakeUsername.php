<?php
namespace App\Helpers\Cron;

use App\Models\User;
use App\Models\Mimic;
use App\Models\MimicResponse;

class FakeUsername
{   
    /**
     * Fake mimic's user and upvote
     */
    public function adjustMimicUpvoteAndUsername()
    {
        //get number of users in the database
        $users = User::count();

        //Prepare query to get ids of admin users
        $adminEmails = ["dario.trbovic@yahoo.com", "traksy_dt@yahoo.com"];

        foreach ($adminEmails as $email) {
            $tmp[] = "email='$email'";
        }

        $emailQuery = implode(" OR ", $tmp);
        $adminIDs = User::whereRaw($emailQuery)->pluck('id')->toArray();
        $adminIDs = implode(", ", $adminIDs);

        if(!empty($adminIDs)) {
            $query = 'created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) AND user_id IN ('.$adminIDs.')';

            //find all mimics where id is admin id and update user_id and upvote
            $results = Mimic::whereRaw($query)->get();

            foreach ($results as $result) {
               $result->user_id = rand(1, 95);
               $result->upvote = rand(1, 103);
               $result->save();
            }

            //find all mimic responses where id is admin id and update user_id and upvote
            $results = MimicResponse::whereRaw($query)->get();

            foreach ($results as $result) {
               $result->user_id = rand(1, 95);
               $result->upvote = rand(1, 103);
               $result->save();
            }
        }
    }
}