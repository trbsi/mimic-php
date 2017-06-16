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
        $this->call(UsersTable::class);
        $this->call(HashtagsTable::class);
        $this->call(MimicsTable::class);
        $this->call(MimicResponseTable::class);
        $this->call(MimicHashtagTable::class);
        $this->call(MimicUpvoteTable::class);
        $this->call(MimicUserTagTable::class);
        $this->call(FollowTable::class);
    }
}
