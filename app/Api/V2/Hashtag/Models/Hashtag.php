<?php 
namespace App\Api\V2\Hashtag\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\Hashtag\Traits\HashtagQueryTrait;

class Hashtag extends Model
{
    use HashtagQueryTrait;
    
    /**
     * Generated
     */

    protected $table = 'hashtags';
    protected $fillable = ['id', 'name', 'popularity'];
    protected $casts =
        [
            'id' => 'int',
        ];

    /**
     * Get popularity attribute
     *
     * @param integer $value Popularity number
     * @return string Formatted number
     */
    public function getPopularityAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return number_format($value);
        }
    }

    public function mimics()
    {
        return $this->belongsToMany(\App\Api\V2\Mimic\Models\Mimic::class, 'mimic_hashtag', 'hashtag_id', 'mimic_id');
    }

    public function mimicHashtags()
    {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicHashtag::class, 'hashtag_id', 'id');
    }
}
