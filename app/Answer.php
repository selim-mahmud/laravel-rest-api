<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Answer
 *
 * @package App
 * @property int $id
 * @property string $reference
 * @property int $question_id
 * @property int $user_id
 * @property string $description
 * @property bool $excepted
 * @property int $up_vote
 * @property int $down_vote
 */
class Answer extends ReferencedModel
{
    const ID = 'id';
    const REFERENCE = 'reference';
    const QUESTION_ID = 'question_id';
    const USER_ID = 'user_id';
    const DESCRIPTION = 'description';
    const EXCEPTED = 'excepted';
    const UP_VOTE = 'up_vote';
    const DOWN_VOTE = 'down_vote';

    const RELATION_USER = 'user';
    const RELATION_QUESTION = 'question';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        self::ID,
    ];

    /**
     * The user who own this answer.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The question which has this answer.
     *
     * @return BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
