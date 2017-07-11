<?php

use Illuminate\Database\Seeder;
use App\Models\Mimic;

class MimicsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Mimic $model)
    {
        $data =
            [
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aNAyE7v_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 1
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aDWBeLG_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_private' => 1,
                    'upvote' => rand(1, 100),
                    'user_id' => 2
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/avGbKqd_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 3
                ],
            ];

        foreach ($data as $key => $value) {
            $model->create($value);
        }
    }
}
