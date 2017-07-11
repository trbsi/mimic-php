<?php

use Illuminate\Database\Seeder;
use App\Models\MimicResponseUpvote;
use App\Models\User;

class MimicResponseUpvoteTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicResponseUpvote $model, User $user)
    {
        $numberOfUsers = $user->count();
        $mimic_id = 1;
        for ($i = 0; $i < 50; $i++) {
            if ($mimic_id > 7) {
                $mimic_id = 1;
            }

            $insert =
            [
                'mimic_id' => $mimic_id,
                'user_id' => rand(1, $numberOfUsers),
            ];
            
            try
            {
                $model->create($insert);
                $mimic_id++;
            }
            catch(\Exception $e)
            {
                
            }
        }
    }
}
