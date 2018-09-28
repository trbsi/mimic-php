<?php

use Illuminate\Database\Seeder;
use App\Models\CoreUser;

class UpvotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed upvotes for one original and one response with all users
     *
     * @return void
     */
    public function run(CoreUser $user)
    {
        $userIds = $user->get()->pluck('id')->toArray();
        $mimicId = 1;
        
        $mimic = resolve('MimicModel');
        $mimic->find($mimicId)->upvotes()->attach($userIds);

        $responses = resolve('ResponseModel');
        $responses->find($mimicId)->upvotes()->attach($userIds);
    }
}
