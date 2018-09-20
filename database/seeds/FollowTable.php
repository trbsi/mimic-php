<?php

use Illuminate\Database\Seeder;
use App\Models\CoreUser;

class FollowTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CoreUser $user)
    {
        $follow = resolve('FollowModel');

        $items =
        [
            //followings of user 1
            [
                'followed_by' => 1,
                'following' => 2,
            ],
            [
                'followed_by' => 1,
                'following' => 3,
            ],
            [
                'followed_by' => 1,
                'following' => 4,
            ],
            //followers of user 1
            [
                'followed_by' => 10,
                'following' => 1,
            ],
            [
                'followed_by' => 11,
                'following' => 1,
            ],
            [
                'followed_by' => 12,
                'following' => 1,
            ],
            
        ];

        foreach ($items as $item) {
            $follow->create($item);
        }
    }
}
