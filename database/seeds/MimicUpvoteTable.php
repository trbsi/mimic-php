<?php

use Illuminate\Database\Seeder;
use App\Models\MimicUpvote;

class MimicUpvoteTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicUpvote $model)
    {
        $mimic_id = 1;
        for ($i = 0; $i < 50; $i++) {
            if ($mimic_id > 10) {
                $mimic_id = 1;
            }

            $insert =
            [
                'mimic_id' => $mimic_id,
                'user_id' => rand(1, 10),
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
