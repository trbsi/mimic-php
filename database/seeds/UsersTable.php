<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        for ($i = 1; $i <= 10; $i++) {

            $insert =
                [
                    'email' => 'user' . $i . '@mail.com',
                    'username' => "user$i",
                    'following' => rand(100, 2000),
                    'followers' => rand(50, 3000),
                    'number_of_mimics' => rand(50,300),
                ];

            $socialAccounts = 
            [
                [
                    'provider' => rand(0,1)%2 ? 'facebook' : 'twitter',
                    'provider_id' => rand()
                ]
            ];

            $userTmp = $user->create($insert);
            $userTmp->socialAccounts()->createMany($socialAccounts);
        }

    }
}
