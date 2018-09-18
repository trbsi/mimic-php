<?php

namespace App\Api\V2\Mimic\Resources\Response\Resources\Meta\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $mimic_id
 * @property int $width
 * @property int $height
 * @property int $thumbnail_width
 * @property int $thumbnail_height
 * @property MimicResponse $mimicResponse
 */
class Meta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mimic_responses_metas';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['mimic_id', 'width', 'height', 'thumbnail_width', 'thumbnail_height', 'color'];

    /**
     * @var array
     */
    protected $casts =
    [
        'id' => 'int',
        'width' => 'int',
        'height' => 'int',
        'thumbnail_width' => 'int',
        'thumbnail_height' => 'int',
        'color' => 'string'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'mimic_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function response()
    {
        return $this->belongsTo('App\Api\V2\Mimic\Models\MimicResponse', 'mimic_id');
    }
}
