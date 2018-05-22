<?php

use App\Models\CoreUser;
use Illuminate\Database\Seeder;

class UsersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CoreUser $user)
    {
        //get number of folders
        $usernames = include __DIR__ . "/FakeUsernames.php";

        for ($i = 1; $i < count($usernames); $i++) {

            $gender = 'female';

            //because of functional testing
            switch($usernames[$i]) {
                case 'beachdude':
                    $gender = 'female';
                    break;
                case 'AndrewCG':
                    $gender = 'male';
                    break;
            }
            
            $insert =
                [
                    'email' => 'user' . $i . '@mail.com',
                    'username' => $usernames[$i],
                    'following' => 123456789,
                    'followers' => 123456789,
                    'number_of_mimics' => 123456789,
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
