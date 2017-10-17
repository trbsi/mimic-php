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
        //get number of folders
        $usernames = include __DIR__ . "/FakeUsernames.php";

        for ($i = 1; $i < count($usernames); $i++) {

            $gender = (rand(0, 1) % 2 == 0) ? 'male' : 'female';
            $insert =
                [
                    'email' => 'user' . $i . '@mail.com',
                    'username' => $usernames[$i],
                    'following' => 0,
                    'followers' => 0,
                    'number_of_mimics' => rand(5, 15),
                    'profile_picture' => env('APP_URL') . '/files/hr/' . $gender . '/' . $i . '.jpg',
                ];

            $socialAccounts =
                [
                    [
                        'provider' => rand(0, 1) % 2 ? 'facebook' : 'twitter',
                        'provider_id' => rand()
                    ]
                ];

            $userTmp = $user->create($insert);
            $userTmp->socialAccounts()->createMany($socialAccounts);
        }

    }
}
