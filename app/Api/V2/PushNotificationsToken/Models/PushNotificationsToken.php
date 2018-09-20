<?php
namespace App\Api\V2\PushNotificationsToken\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\User\Models\User;

class PushNotificationsToken extends Model
{

    /**
     * Generated
     */

    protected $table = 'push_notifications_token';
    protected $fillable = ['id', 'user_id', 'token', 'device', 'device_id', 'updated_at', 'created_at'];
    protected $casts =
    [
        'id' => 'int',
        'user_id' => 'int',
    ];

    /**
     * get push tokens of a user so you can send notification to him
     * @param int $userId - ID of a user
     * @return PushNotificationsToken[]|array
     */
    public static function getNotificationTokens(int $userId)
    {
        return PushNotificationsToken::where('user_id', $userId)->orderBy("updated_at", "DESC")->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
