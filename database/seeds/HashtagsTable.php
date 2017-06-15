<?php

use Illuminate\Database\Seeder;
use App\Models\Hashtag;

class HashtagsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Hashtag $model)
    {
        $data =
            [
                '#jumping', '#playingaround', '#kissing', '#comewithme', '#playingsport', '#meandmycrew', '#dance', '#swim', '#yolo', '#swag',
            ];

        foreach ($data as $key => $value) {
            $insert =
                [
                    'popularity' => rand(1, 100),
                    'name' => $value
                ];

            $model->create($insert);
        }
    }
}
