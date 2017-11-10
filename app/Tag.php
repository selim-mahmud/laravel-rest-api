<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Tag
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $display_name
 */
class Tag extends Model
{
    const ID = 'id';
    const NAME = 'name';
    const SLUG = 'slug';
    const DISPLAY_NAME = 'display_name';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    /**
     * The questions that belong to the tag.
     *
     * @return BelongsToMany
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

}
