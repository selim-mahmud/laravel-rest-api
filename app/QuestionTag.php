<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class QuestionTag
 *
 * @package App
 * @property int $question_id
 * @property int $tag_id
 */
class QuestionTag extends Model
{
    const QUESTION_ID = 'question_id';
    const TAG_ID = 'tag_id';
}
