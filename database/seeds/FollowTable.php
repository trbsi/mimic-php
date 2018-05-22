<?php

use Illuminate\Database\Seeder;
use App\Models\CoreUser;
use App\Api\V2\Follow\Models\Follow;

class FollowTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CoreUser $user, Follow $follow)
    {
        $items =
        [
            [
                'followed_by' => 1,
                'following' => 2,
            ],
            [
                'followed_by' => 2,
                'following' => 1,
            ]
            
        ];

        foreach ($items as $item) {
            $follow->create($item);
        }
    }
}
