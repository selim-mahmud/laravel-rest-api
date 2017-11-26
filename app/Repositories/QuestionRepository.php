<?php

namespace App\Repositories;

use App\Question;

class QuestionRepository extends ReferencedModelRepository
{
    /**
     * QuestionRepository constructor.
     *
     * @param Question $question
     */
    function __construct(Question $question) {
        $this->setModel($question);
    }
}