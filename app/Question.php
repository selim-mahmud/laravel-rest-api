<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Question
 *
 * @package App
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property bool $featured
 * @property bool $sticky
 * @property bool $solved
 * @property int $up_vote
 * @property int $down_vote
 */
class Question extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const TITLE = 'title';
    const SLUG = 'slug';
    const DESCRIPTION = 'description';
    const FEATURED = 'featured';
    const STICKY = 'sticky';
    const SOLVED = 'solved';
    const UP_VOTE = 'up_vote';
    const DOWN_VOTE = 'down_vote';

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
     * The user who own this question.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tags that belong to the question.
     *
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
