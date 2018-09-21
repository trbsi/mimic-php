<?php 
namespace App\Api\V2\Hashtag\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\Hashtag\Traits\HashtagQueryTrait;
use App\Api\V2\Mimic\Models\Mimic;

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
        return $this->belongsToMany(Mimic::class, db_table('mimic_hashtag'), 'hashtag_id', 'mimic_id');
    }
}
