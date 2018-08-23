<?php

namespace App\Api\V2\Mimic\Repositories;

use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use App\Helpers\SendPushNotification;
use App\Helpers\Constants;
use DB;
use App\Events\Mimic\MimicUpvotedEvent;

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
     * @param array $data Array of data from request
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

        DB::beginTransaction();

        //try to upvote
        try {
            $model->increment('upvote');
            $model->userUpvotes()->attach($user->id);
            DB::commit();

            $type = Constants::UPVOTED;
            event(new MimicUpvotedEvent($model, $user, $data));
        } //downvote
        catch (\Exception $e) {
            DB::rollBack();

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
