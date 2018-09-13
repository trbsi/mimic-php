<?php

namespace App\Api\V2\User\Resources\Profile\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Api\V2\User\Resources\Profile\Requests\ProfileRequest;
use App\Api\V2\User\Resources\Profile\Repositories\UpdateProfileRepository;

class ProfileController extends BaseAuthController
{
	/**
	 * @param  ProfileRequest          $request                 
	 * @param  UpdateProfileRepository $updateProfileRepository 
	 * @return Response
	 */
	public function update(ProfileRequest $request, UpdateProfileRepository $updateProfileRepository)
	{
		try {
			$updateProfileRepository->update($this->authUser, $request->all());
			return response()->json([], 204);
		} catch (Exception $e) {
			throw_error($e);
		}
	}
}