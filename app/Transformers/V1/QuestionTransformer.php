<?php

namespace App\Transformers\V1;

use App\Transformers\Transformer;
use App\Question;

class QuestionTransformer extends Transformer
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
     * @inheritdoc
     * @param Question $item
     */
    public function getTransformationMap($item) : array
    {
        return [
            self::ID    => $item->getAttribute(Question::REFERENCE),
            self::USER_ID  => $item->getAttribute(Question::USER_ID),
            self::TITLE  => $item->getAttribute(Question::TITLE),
            self::SLUG  => $item->getAttribute(Question::SLUG),
            self::DESCRIPTION  => $item->getAttribute(Question::DESCRIPTION),
            self::FEATURED  => $item->getAttribute(Question::FEATURED),
            self::STICKY  => $item->getAttribute(Question::STICKY),
            self::SOLVED  => $item->getAttribute(Question::SOLVED),
            self::UP_VOTE  => $item->getAttribute(Question::UP_VOTE),
            self::DOWN_VOTE  => $item->getAttribute(Question::DOWN_VOTE),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getBasicTransformationFields(): array
    {
        return [
            self::ID,
            self::USER_ID,
            self::TITLE,
            self::SLUG,
            self::DESCRIPTION,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getFullTransformationFields(): array
    {
        return [
            self::ID,
            self::USER_ID,
            self::TITLE,
            self::SLUG,
            self::DESCRIPTION,
            self::FEATURED,
            self::STICKY,
            self::SOLVED,
            self::UP_VOTE,
            self::DOWN_VOTE,
        ];
    }
}