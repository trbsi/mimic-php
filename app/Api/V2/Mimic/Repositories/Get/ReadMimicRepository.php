<?php

namespace App\Api\V2\Mimic\Repositories\Get;

use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Resources\Response\Models\Response;
use Illuminate\Http\Request;

final class ReadMimicRepository
{
    public function __construct(Mimic $mimic, Response $response)
    {
        $this->mimic = $mimic;
        $this->response = $response;
    }

    public function getUserMimics(Request $request, User $authUser)
    {
        $relations = ['meta'];
        if ($request->get_responses && ($request->get_responses === 'true' || $request->get_responses === true)) {
            $relations = array_merge($relations, ['originalMimic']);
            $model = $this->response;
        } else {
            $model = $this->mimic;
        }

        if ($request->user_id) {
            $user_id = $request->user_id;
        } else {
            $user_id = $authUser->id;
        }

        return $model->where('user_id', $user_id)
        ->orderBy('id', 'DESC')
        ->with($relations)
        ->get();
    }
}
