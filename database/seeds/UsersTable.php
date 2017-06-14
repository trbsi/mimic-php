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
        $data =
        [
        	[
        		'email' => 'user1@mail.com',
                'facebook_id' => mt_rand(),
                'username' => "user_1",
        	],

        ];

        foreach ($data as $key => $value) 
        {
        	$user->create($value);
        }
    }
}
