<?php
namespace App\Helpers\Cron;

use App\Models\User;
use App\Models\Mimic;
use App\Models\MimicResponse;

class FakeMimicData
{
    /**
     * Fake mimic's user and upvote
     */
    public function adjustMimicData()
    {
        //$query = 'created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND (user_id >= 1 AND user_id <= 97)';
        $query = 'created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)';

        //find all mimics where id is admin id and update user_id and upvote
        $results = Mimic::whereRaw($query)->get();

        foreach ($results as $result) {
           $result->upvote = rand(1, 26);
           $result->save();
        }

        //find all mimic responses where id is admin id and update user_id and upvote
        $results = MimicResponse::whereRaw($query)->get();

        foreach ($results as $result) {
           $result->upvote = rand(1, 26);
           $result->save();
        }
        
    }
}