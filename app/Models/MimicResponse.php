<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mimic;
use App\Traits\MimicTrait;
use App\Models\MimicResponseUpvote;

class MimicResponse extends Model
{
    use MimicTrait;

    /**
     * Generated
     */

    protected $table = 'mimic_response';
    protected $fillable = ['id', 'original_mimic_id', 'file', 'aws_file', 'mimic_type', 'upvote', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'mimic_type' => 'int',
            'upvote' => 'int',
            'user_id' => 'int',
            'original_mimic_id' => 'int'
        ];

    /**
     * Get file with full path and url
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getFileAttribute($value)
    {
        return $this->getFileOrPath($this->user, $value, $this, true);
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
     * get all mimic responses of a specific original mimic
     * @param  $request
     * @param  $authUser Model of an authenitcated user
     * @return data from the database
     */
    public function getMimicResponses($request, $authUser)
    {
        $offset = 0;
        if ($request->page) {
            $offset = Mimic::LIST_RESPONSE_MIMICS_LIMIT * $request->page;
        }

        return $this
            ->select($this->getTable() . ".*")
            ->selectRaw("IF(EXISTS(SELECT null FROM " . (new MimicResponseUpvote)->getTable() . " WHERE user_id=$authUser->id AND mimic_id = " . $this->getTable() . ".id), 1, 0) AS upvoted")
            ->where("original_mimic_id", $request->original_mimic_id)
            ->orderBy("upvote", "DESC")
            ->limit(Mimic::LIST_RESPONSE_MIMICS_LIMIT)
            ->offset($offset)
            ->with(['user'])
            ->get();

    }

    public function mimic()
    {
        return $this->belongsTo(\App\Models\Mimic::class, 'original_mimic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function userUpvotes()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mimic_response_upvote', 'mimic_id', 'user_id')->withTimestamps();
    }

    public function upvotes()
    {
        return $this->hasMany(\App\Models\MimicResponseUpvote::class, 'mimic_id', 'id');
    }


}
