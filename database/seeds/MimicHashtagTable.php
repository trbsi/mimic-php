<?php

use Illuminate\Database\Seeder;

class MimicHashtagTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data =
        [
        	[
        		'' => '',
        	],

        ];

        foreach ($data as $key => $value) 
        {
        	$user->create($value);
        }
    }
}
