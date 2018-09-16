<?php

namespace App\Api\V2\User\Resources\Profile\Controllers;

use App\Api\V2\Auth\Controllers\BaseAuthController;
use App\Api\V2\User\Resources\Profile\Requests\ProfileRequest;
use App\Api\V2\User\Resources\Profile\Repositories\Put\UpdateProfileRepository;
use App\Api\V2\User\Resources\Profile\Repositories\Get\GetProfileRepository;
use Illuminate\Http\Request;

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
            $user = $updateProfileRepository->update($this->authUser, $request->all());
            return response()->json($user);
        } catch (Exception $e) {
            throw_error($e);
        }
    }

    /**
     * Get user profile and return data
     * @param Request $request
     * @param GetProfileRepository $getProfileRepository
     * @throws Exception
     * @return Response
     */
    public function get(Request $request, GetProfileRepository $getProfileRepository)
    {
        try {
            $user = $getProfileRepository->getProfile($request->all(), $this->authUser, $this->user);
            return response()->json($user);
        } catch (Exception $e) {
            throw_exception($e);
        }
    }
}
