<?php 
namespace App\Api\V2\Mimic\Models;

use Illuminate\Database\Eloquent\Model;
use App\Api\V2\Mimic\Models\Mimic;
use App\Api\V2\Mimic\Traits\MimicTrait;
use App\Api\V2\Mimic\Models\MimicResponseUpvote;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helper;

class MimicResponse extends Model
{
    use MimicTrait, SoftDeletes;

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'original_mimic_id', 'file', 'aws_file', 'mimic_type', 'upvote', 'user_id', 'width', 'height', 'aws_video_thumb'];

    protected $appends = ['file_url', 'video_thumb_url'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_type' => 'int',
            'upvote' => 'int',
            'user_id' => 'int',
            'original_mimic_id' => 'int',
            'upvoted' => 'int', //this is to check if user upvoted mimic or not
            'i_am_following_you' => 'boolean', //when returning the list of response mimics
        ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    /**
     * Get file with full path and url
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getFileUrlAttribute($value)
    {
        return $this->getFileOrPath($this->user_id, $this->file, $this, true);
    }

    /**
     * Get video thumb with full path and url
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getVideoThumbUrlAttribute($value)
    {
        if ($this->video_thumb) {
            return $this->getFileOrPath($this->user_id, $this->video_thumb, $this, true);
        }

        return null;
    }

    /**
     * get mimic type: video, image
     * @param  Integer $value Mimic type 0 or 1
     * @return String "video/picture"
     */
    public function getMimicTypeAttribute($value)
    {
        return $this->getMimicType($value);
    }

    /**
     * Format upvote attribute to be nicer like: 12k, 369M...
     * @param  integer $value A number of upvotes
     * @return string
     */
    public function getUpvoteAttribute($value)
    {
        if ($this->preventMutation) {
            return $value;
        } else {
            return Helper::numberFormat($value);
        }
    }

    /**
     * get all mimic responses of a specific original mimic
     * order by most popular
     * @param  $request
     * @param  $authUser Model of an authenitcated user
     * @return data from the database
     */
    public function getMimicResponses($request, $authUser)
    {
        return $this
        ->select($this->getTable() . ".*")
        ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicResponseUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = " . $this->getTable() . ".id), 1, 0) AS upvoted")
        ->where("original_mimic_id", $request->original_mimic_id)
        ->orderBy("id", "DESC")
        ->with(['user'])
        ->paginate(Mimic::LIST_RESPONSE_MIMICS_LIMIT);
    }

    public function originalMimic()
    {
        return $this->belongsTo(\App\Api\V2\Mimic\Models\Mimic::class, 'original_mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Api\V2\User\Models\User::class, 'user_id', 'id');
    }

    public function userUpvotes()
    {
        return $this->belongsToMany(\App\Api\V2\User\Models\User::class, 'mimic_response_upvote', 'mimic_id', 'user_id')->withTimestamps();
    }

    public function upvotes()
    {
        return $this->hasMany(\App\Api\V2\Mimic\Models\MimicResponseUpvote::class, 'mimic_id', 'id');
    }
}
