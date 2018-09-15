<?php

namespace App\Api\V2\User\Repositories;

use App\Api\V2\User\Models\User;

class UpdateRepository
{	
	/**
	 * @param  User   $authUser 
	 * @param  array  $data     
	 * @return void           
	 */
	public function update(User $authUser, array $data)
	{
		$authUser->update($data);
	}
}