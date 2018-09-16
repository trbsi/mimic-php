<?php
namespace App\Cron\Fakes\Mimics\Upvotes;

use Illuminate\Support\Facades\Redis;
use App\Cron\Fakes\Mimics\Upvotes\FakeUpvotes;
use App\Models\CoreUser;

/**
 * Reinserts users, mimics and responses so we can start with fake upvotes from the beginning
 */
class ResetFakeUpvotes
{
    public function run()
    {
        //delete from set
        $keys = [FakeUpvotes::REDIS_MIMIC_NOTIFICATIONS, FakeUpvotes::REDIS_RESPONSES_NOTIFICATIONS, FakeUpvotes::REDIS_USER_IDS];
        $this->delete($keys);

        //insert newest mimics in redis with score 0
        $this->insert(resolve('MimicModel'), FakeUpvotes::REDIS_MIMIC_NOTIFICATIONS);
        $this->insert(resolve('MimicResponseModel'), FakeUpvotes::REDIS_RESPONSES_NOTIFICATIONS);

        //insert userids in set
        $this->insertUserIds();
    }

    
    /**
     * @param string $key
     * @return string
     */
    public static function getFileName(string $key): string
    {
        return sprintf('app/fakes/%s.json', $key);
    }

    /**
     * @param array $keys
     * @return void
     */
    private function delete(array $keys): void
    {
        foreach ($keys as $key) {
            $file = self::getFileName($key);
            if (file_exists(storage_path($file))) {
                unlink(storage_path($file));
            }
        }

        if (!file_exists(storage_path('app/fakes'))) {
            mkdir(storage_path('app/fakes'), 0755, true);
        }
    }

    /**
     * @param object $model
     * @param string $key
     * @return void
     */
    private function insert(object $model, string $key): void
    {
        $data = [];
        $result = $model
        ->orderBy('id', 'DESC')
        ->limit(50);

        if ($key === FakeUpvotes::REDIS_RESPONSES_NOTIFICATIONS) {
            $result = $result->has('originalMimic');
        }

        foreach ($result->get() as $mimic) {
            $data[] = [
                'id' => $mimic->id,
                'time' => 0,
                'stage' => 1,
            ];
        }

        $file = self::getFileName($key);
        file_put_contents(storage_path($file), json_encode($data));
    }

    /**
     * @return void
     */
    private function insertUserIds()
    {
        $data = [];
        $users = CoreUser::get();
        foreach ($users as $user) {
            $data[] = $user->id;
        }

        $file = self::getFileName(FakeUpvotes::REDIS_USER_IDS);
        file_put_contents(storage_path($file), json_encode($data));
    }
}
