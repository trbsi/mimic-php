<?php

use Illuminate\Database\Seeder;
use App\Models\MimicTaguser;

class MimicUserTagTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicTaguser $model)
    {
        $mimic_id = 1;
        for ($i = 0; $i < 30; $i++) {
            if ($mimic_id > 3) {
                $mimic_id = 1;
            }

            $insert =
            [
                'mimic_id' => $mimic_id,
                'user_id' => rand(1, 10),
            ];
            $model->create($insert);
            $mimic_id++;
        }
    }
}
