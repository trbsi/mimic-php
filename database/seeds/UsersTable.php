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
                    'facebook_id' => mt_rand(),
                    'username' => "user_$i",
                ];

            $user->create($insert);
        }

    }
}
