<?php

use Illuminate\Database\Seeder;
use App\Models\MimicResponse;
use App\Models\Mimic;

class MimicResponseTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(MimicResponse $model)
    {

        $data = 
        [
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/aPBObDG_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/a6bWYnq_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/ajX2e1Q_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/aAdnz0g_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/avGbKAn_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/a8yrxGQ_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
            [
                'file' => 'https://img-9gag-fun.9cache.com/photo/aVqG65M_460sv.mp4',
                'mimic_type' => Mimic::TYPE_VIDEO,
                'user_id' => rand(4,10),
            ],
        ];

        for ($i=0; $i < 10; $i++) { 
            foreach ($data as $key => $value) {
                $value['upvote'] = rand(1, 100);
                $value['original_mimic_id'] = rand(1, 3);
                $model->create($value);
            }
        }
        
    }
}
