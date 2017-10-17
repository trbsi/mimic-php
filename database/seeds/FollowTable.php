<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Follow;

class FollowTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user, Follow $follow)
    {
        $numberOfUsers = $user->count();
        for ($i = 1; $i <= 200; $i++) {
            $insert =
                [
                    'followed_by' => rand(1, $numberOfUsers),
                    'following' => rand(1, $numberOfUsers),
                ];

            try {
                $follow->create($insert);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
            }
        }

        //update user's following and followers
        foreach ($user->all() as $user) {
            $followers = $follow->where('following', $user->id)->count();  //number of followers
            $following = $follow->where('followed_by', $user->id)->count(); //number of user I'm following

            $user->update(['following' => $following, 'followers' => $followers]);
        }
    }
}
