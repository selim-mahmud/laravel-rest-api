<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Tag
 *
 * @package App
 * @property int $id
 * @property string $reference
 * @property string $name
 * @property string $slug
 * @property string $active
 */
class Tag extends ReferencedModel
{
    const ID = 'id';
    const REFERENCE = 'reference';
    const NAME = 'name';
    const SLUG = 'slug';
    const ACTIVE = 'active';

    const  RELATION_QUESTIONS = 'questions';

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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        self::ACTIVE => 'boolean',
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
