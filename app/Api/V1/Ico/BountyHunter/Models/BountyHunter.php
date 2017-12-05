<?php 

namespace App\Api\V1\Ico\BountyHunter\Models;

use Illuminate\Database\Eloquent\Model;

class BountyHunter extends Model
{
    const REWARD_WHITEPAPER = "1000";
    const REWARD_TOPIC_MANAGER = "750 translation + 50 per post";
    const REWARD_ARTCILE = "250 - 1000";
    protected $table = 'bounty_hunters';
    protected $fillable = ['contribution_type', 'forum', 'forum_nickname', 'email', 'ethereum_address', 'reward', 'previous_work'];
    protected $casts =
    [
        'id' => 'int',
    ];

    public function getContributionTypeAttribute($value)
    {
        return $this->contributionTypes()[$value];
    }

    public function getForumAttribute($value)
    {
        return $this->forums()[$value];
    }

    public function contributionTypes()
    {
        return [
            'white_paper' => 'White paper translation', 
            'topic_manager' => 'ANN topic manager',
            'article' => 'Article',
        ]; 
    }

    public function forums()
    {
        return [
            'bitcoin_talk' => 'BitCoinTalk', 
        ]; 
    }
}