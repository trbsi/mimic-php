<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use DB;

class UpvoteMimicRepository
{
    public function __construct(
        Mimic $mimic,
        MimicResponse $mimicResponse
    ) {
        $this->mimic = $mimic;
        $this->mimicResponse = $mimicResponse;
    }

    /**
     * Upvote original or response mimic
     * @param array $request Array of data from request
     * @param object $authUser Authenticated user
     * @return array
     */
    public function upvote(array $data, object $authUser): array
    {
        //@TODO REMOVE - fake user
        $user = $this->mimic->getUser($authUser);
        //@TODO REMOVE - fake user

        $model = array_get($data, 'original_mimic_id') ? $this->mimic : $this->mimicResponse;
        $id = array_get($data, 'original_mimic_id') ?? array_get($data, 'response_mimic_id');
        $model = $model->find($id);
        $model->preventMutation = true;

        if (array_get($data, 'original_mimic_id')) {
            $api_call_params = [
                'user_id' => $model->user_id,
                'original_mimic_id' => $model->id,
            ];
        } else {
            $api_call_params = [
                'user_id' => $model->originalMimic->user_id,
                'original_mimic_id' => $model->original_mimic_id,
                'response_mimic_id' => $model->id,
            ];
        }

        DB::beginTransaction();

        //try to upvote
        try {
            $model->increment('upvote');
            $model->userUpvotes()->attach($user->id);
            DB::commit();

            //send push
            $pushData = [
                'authUser' => $user,
                'parameters' => [
                    'api_call_params' => array_merge(['page' => 1,], $api_call_params),
                    'position' => Constants::POSITION_SPLIT_SCREEN
                ],
            ];

            $this->mimic->sendMimicNotification($model, Constants::PUSH_TYPE_UPVOTE, $pushData);
            $type = Constants::UPVOTED;
        } //downvote
        catch (\Exception $e) {
            DB::rollBack(); //rollback query inside "try"
            $model->decrement('upvote');
            $model->userUpvotes()->detach($user->id);
            $type = Constants::DOWNVOTED;
        }

        $model = $model->fresh();
        return [
            'type' => $type,
            'upvotes' => $model->upvote,
        ];
    }
}
