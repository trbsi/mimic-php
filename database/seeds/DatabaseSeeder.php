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
        $this->call(HashtagsTable::class);
        $this->call(UsersTable::class);
        $this->call(MimicsTable::class);
        $this->call(FollowTable::class);
    }
}
