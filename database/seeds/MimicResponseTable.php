<?php

use Illuminate\Database\Seeder;
use App\Models\MimicResponse;

class MimicResponseTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicResponse $model)
    {
        $original_mimic_id = 1;
        $response_mimic_id = 4;
        for ($i = 0; $i < 50; $i++) {
            if ($original_mimic_id > 3) {
                $original_mimic_id = 1;
            }

            if ($response_mimic_id > 10) {
                $response_mimic_id = 4;
            }

            $insert =
            [
                'original_mimic_id' => $original_mimic_id,
                'response_mimic_id' => $response_mimic_id,
            ];
            
            try
            {
                $model->create($insert);
                $original_mimic_id++;
                $response_mimic_id++;
            }
            catch(\Exception $e)
            {
                
            }
        }
    }
}
