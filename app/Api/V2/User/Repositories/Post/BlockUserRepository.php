<?php

namespace App\Api\V2\User\Repositories\Post;

use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;
use Exception;
use App\Helpers\Constants;

final class BlockUserRepository
{
    /**
     * @param Request $request
     * @param User $authUser
     * @throws Exception
     * @return array
     */
    public function blockUser(Request $request, User $authUser): array
    {
        if ((int)$request->user_id === (int)$authUser->id) {
            abort(400, trans('users.cant_block_yourself'));
        }

        try {
            //block
            $authUser->blockedUsers()->attach($request->user_id);
            $type = Constants::BLOCKED;
        } catch (Exception $e) {
            //unblock
            $authUser->blockedUsers()->detach($request->user_id);
            $type = Constants::UNBLOCKED;
        }
        return ['type' => $type];
    }
}
