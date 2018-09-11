<?php

namespace App\Api\V2\User\Repositories;

use App\Api\V2\User\Models\User;
use App\Api\V2\PushNotificationsToken\Models\PushNotificationsToken;
use App\Api\V2\Mimic\Repositories\DeleteMimicRepository;

class DeleteUserRepository
{
    /**
     * @var DeleteMimicRepository
     */
    private $deleteMimicRepository;

    /**
     * @param DeleteMimicRepository $deleteMimicRepository
     */
    public function __construct(DeleteMimicRepository $deleteMimicRepository)
    {
        $this->deleteMimicRepository = $deleteMimicRepository;
    }

    /**
     * @param  User   $authUser 
     * @param  array  $data     
     * @return void           
     */
    public function delete(User $authUser, array $data)
    {
        //invalidate token
        auth()->invalidate(true);
        //delete mimics
        $this->deleteMimicRepository->deleteUserMimicsAndResponsesByUser($authUser);
        //force delete user
        $authUser->forceDelete();
    }
}
