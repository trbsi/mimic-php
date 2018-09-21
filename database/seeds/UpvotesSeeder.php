<?php

use Illuminate\Database\Seeder;
use App\Models\CoreUser;

class UpvotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CoreUser $user)
    {
    	$userIds = $user->get()->pluck('id')->toArray();
    	
        $mimic = resolve('MimicModel');
        $mimic->find(1)->upvotes()->attach($userIds);

        $responses = resolve('ResponseModel');
        $responses->find(1)->upvotes()->attach($userIds);
    }
}
