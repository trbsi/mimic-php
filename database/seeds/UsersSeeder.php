<?php

use App\Models\CoreUser;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CoreUser $user)
    {
        //get number of folders
        $usernames = include __DIR__ . "/data/FakeUsernames.php";

        for ($i = 1; $i < count($usernames); $i++) {
            $gender = 'female';

            //because of functional testing
            switch ($usernames[$i]) {
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
            $this->createProfile($userTmp);
        }
    }

    /**
     * @param User $user
     * @return void
     */
    private function createProfile($user)
    {
        $hashtags =
        [
            '#swag', '#kissing', '#dance'
        ];

        $profile =
        [
            'bio' => sprintf("This is my bio, which is little bit too big. I even use emojis and %s. 😀 😁 😂 \nI need to check it out! I Like %s and %s", $hashtags[0], $hashtags[1], $hashtags[2])
        ];

        $profile = $user->profile()->create($profile);

        $this->saveBioHashtags($profile, $hashtags);
    }

    /**
     * @param  Profile $profile
     * @param  array  $hashtags
     * @return void
     */
    private function saveBioHashtags($profile, array $hashtags)
    {
        $model = resolve('HashtagModel');
        $ids = $model->whereIn('name', $hashtags)->get()->pluck('id')->toArray();
        $profile->hashtags()->attach($ids);
    }
}
