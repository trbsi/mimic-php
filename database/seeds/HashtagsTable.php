<?php

use Illuminate\Database\Seeder;
use App\Api\V2\Hashtag\Models\Hashtag;

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
            '#jumping', '#jump','#playingaround', '#kissing', '#comewithme', '#playingsport', '#meandmycrew', '#dance', '#swim', '#yolo', '#swag',
        ];

        foreach ($data as $value) {
            $insert =
            [
                'popularity' => 123456789,
                'name' => $value
            ];

            $model->create($insert);
        }
    }
}
