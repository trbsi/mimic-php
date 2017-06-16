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
    public function run(User $user, Follow $model)
    {
        $numberOfUsers = $user->count();
        for ($i = 0; $i < 100; $i++) {
            $insert =
            [
                'followed_by' => rand(1, $numberOfUsers),
                'following' => rand(1, $numberOfUsers),
            ];

            try
            {
                $model->create($insert);
            }
            catch(\Exception $e)
            {
                //var_dump($e->getMessage());
            }
        }
    }
}
