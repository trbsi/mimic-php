<?php
namespace App\Cron\Fakes\Mimics\Upvotes;

use App\Api\V2\User\Models\User;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Models\MimicResponse;
use Illuminate\Support\Facades\Redis;
use App\Events\Mimic\MimicUpvotedEvent;
use App\Models\CoreUser;
use App\Cron\Fakes\Mimics\Upvotes\ResetFakeUpvotes;

class FakeUpvotes
{
    public const REDIS_MIMIC_NOTIFICATIONS = 'mimic_notifications';
    public const REDIS_RESPONSES_NOTIFICATIONS = 'responses_notifications';
    public const REDIS_USER_IDS = 'user_ids';

    /**
     * @var array
     */
    private static $users = null;

    /**
     * Fake mimic's upvotes
     */
    public function run()
    {
        $keys = [self::REDIS_MIMIC_NOTIFICATIONS, self::REDIS_RESPONSES_NOTIFICATIONS];

        $mimicIds = [];
        foreach ($keys as $key) {
            $mimics = $this->getDataFromJson($key);
            if ($mimics === null) {
                continue;
            }
            
            foreach (array_rand($mimics, 5) as $randomKey) {
                $mimic = $mimics[$randomKey];
                //send notifications
                $status = $this->handleNotification($key, $mimic);
                if ($status === true) {
                    //update mimics with new data
                    $mimics[$randomKey]['stage'] = ++$mimic['stage'];
                    $mimics[$randomKey]['time'] = time();
                }
            }

            $mimics = $this->appendNewMimics($mimics, $key);

            //update json file
            $file = ResetFakeUpvotes::getFileName($key);
            file_put_contents(storage_path($file), json_encode($mimics));
        }
    }

    /**
     * @param array $mimics
     * @param string $key
     * @return array
     */
    private function appendNewMimics(array $mimics, string $key): array 
    {
        $mimicIds = array_column($mimics,'id');

        switch ($key) {
            case self::REDIS_MIMIC_NOTIFICATIONS:
                $model = resolve('MimicModel');
                break;
            case self::REDIS_RESPONSES_NOTIFICATIONS:
                $model = resolve('MimicResponseModel');
                break;
        }

        //find new mimics (newest than 30 min ago) if there are any and include into file
        $time = date('Y-m-d H:i:s', time() - 1800);
        $results = $model->whereRaw(sprintf('created_at >= "%s"', $time))->get();

        foreach ($results as $mimic) {
            if (!in_array($mimic->id, $mimicIds)) {
                $mimics[]= [
                    'id' => $mimic->id,
                    'time' => 0,
                    'stage' => 1
                ];
            }
        }

        return $mimics;
    }

    /**
     * @param string $key
     * @return array|null
     */
    private function getDataFromJson(string $key): ?array 
    {
        $file = ResetFakeUpvotes::getFileName($key);
        $filePath = storage_path($file);
        if (!file_exists($filePath)) {
            return null;
        }
        return json_decode(file_get_contents($filePath), true);
    }

    /**
     * Score is a combination of time and stage: "time().stage"
     * 
     * @param string $key
     * @param array $mimic
     * @return boolean
     */
    private function handleNotification(string $key, array $mimic): bool 
    {
        $sendNotificationCount = false;
        $timeDiff = (time() - $mimic['time']) / 60; //minutes 

        switch ($mimic['stage']) {
            case 1:
                $sendNotificationCount = 1;
                break;
            case 2:
                if ($timeDiff >= 15 && $timeDiff <= 30) {
                    $sendNotificationCount = 2;
                }
                break;
            case 3:
                if ($timeDiff >= 60 && $timeDiff <= 180) {
                    $sendNotificationCount = rand(1, 2);
                }
                break;
            case 4:
                if ($timeDiff >= 240 && $timeDiff <= 360) {
                    $sendNotificationCount = rand(1, 2);
                }
                break;
        }

        if ($sendNotificationCount !== false) {
            for ($i = 1; $i <= $sendNotificationCount; $i++) {
                $this->sendNotification($key, $mimic);
            }
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * @param array $mimic
     * @return void
     */
    private function sendNotification(string $key, array $mimic): void
    {
        //get rand user who will "upvote" mimic
        if(!self::$users) {
            self::$users = $this->getDataFromJson(self::REDIS_USER_IDS);
        }

        switch ($key) {
            case self::REDIS_MIMIC_NOTIFICATIONS:
                $model = resolve('MimicModel');
                $data = ['original_mimic_id' => $mimic['id']];
                break;
            case self::REDIS_RESPONSES_NOTIFICATIONS:
                $model = resolve('MimicResponseModel');
                $data = ['response_mimic_id' => $mimic['id']];
                break;
        }

        $model = $model->find($mimic['id']);
        $model->preventMutation = true;
        $model->increment('upvote');
        $user = $this->getRandomUser($model);
        event(new MimicUpvotedEvent($model, $user, $data));
    }

    /**
     * @param object $mimic
     * @return CoreUser
     */
    private function getRandomUser(object $mimic)
    {
        $randKey = array_rand(self::$users);
        //it won't be appropriate if user upvotes his own mimic
        if (self::$users[$randKey] == $mimic->user_id) {
            $this->getRandomUser($mimic);
        }
        
        return CoreUser::find(self::$users[$randKey]);
    }
}