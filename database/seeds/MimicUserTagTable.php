<?php

use Illuminate\Database\Seeder;
use App\Models\MimicTaguser;
use App\Models\User;

class MimicUserTagTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicTaguser $model, User $user)
    {
        $numberOfUsers = $user->count();
        $mimic_id = 1;
        for ($i = 0; $i < 10; $i++) {
            if ($mimic_id > 3) {
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
