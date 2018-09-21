<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(HashtagsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(MimicsSeeder::class);
        $this->call(UpvotesSeeder::class);
        $this->call(FollowsSeeder::class);
    }
}
