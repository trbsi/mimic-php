<?php
namespace App\Api\V2\User\Traits;

trait UserQueryTrait
{	
	/**
	 * Return top 10 hashtags
	 * 
	 * @return collection
	 */
	public function getTopTenUsers()
	{
		return $this
		->orderBy('followers', 'DESC')
		->orderBy('number_of_mimics', 'DESC')
		->limit(10)
		->get();
	}
}