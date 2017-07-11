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
                    'profile_picture' => (rand(0,1)%2 == 0) ? 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/15439727_156499158165541_8451421707669405420_n.jpg?oh=5d2b5df0c4a4554a0410129437754be1&oe=5A01E5C1' : 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/11193409_846437678737308_1120848662585314240_n.jpg?oh=33b64e0a053a369e899a8f68912fc76d&oe=5A010336',
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
