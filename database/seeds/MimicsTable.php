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
                    'is_response' => 0,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 1
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aDWBeLG_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 0,
                    'is_private' => 1,
                    'upvote' => rand(1, 100),
                    'user_id' => 2
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/avGbKqd_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 0,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 3
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aPBObDG_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 4
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/a6bWYnq_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 5
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/ajX2e1Q_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 6
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aAdnz0g_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 7
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/avGbKAn_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 8
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/a8yrxGQ_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 9
                ],
                [
                    'file' => 'https://img-9gag-fun.9cache.com/photo/aVqG65M_460sv.mp4',
                    'mimic_type' => Mimic::TYPE_VIDEO,
                    'is_response' => 1,
                    'is_private' => 0,
                    'upvote' => rand(1, 100),
                    'user_id' => 10
                ],

            ];

        foreach ($data as $key => $value) {
            $model->create($value);
        }
    }
}
