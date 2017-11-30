<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Resources\V1\QuestionCollection;
use App\Question;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;

class QuestionController extends ApiController
{
    /**
     * @var Question $question
     */
    protected $question;

    /**
     * QuestionController constructor.
     *
     * @param ApiRequest $request
     * @param Question $question
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     */
    public function __construct(
        ApiRequest $request,
        Question $question,
        ApiColumnFilterHandler $columnFilterHandler,
        ApiRelationAdditionHandler $relationAdditionHandler,
        ApiRelationFilterHandler $relationFilterHandler
    )
    {
        parent::__construct(
            $request,
            $columnFilterHandler->setFilterableFields(
                $this->getFilterableFields()
            ),
            $relationAdditionHandler->setAddableRelations(
                $this->getRelationNames()
            ),
            $relationFilterHandler->setRelationNames(
                $this->getRelationNames()
            )
        );

        $this->question = $question;
    }

    /**
     * Display a listing of the resource.
     *
     * @return QuestionCollection
     */
    public function index(): QuestionCollection
    {
        $queryBuilder = $this->question->newQuery();

        return new QuestionCollection($this->getListCollection($queryBuilder));
    }

    /**
     * @return array
     */
    protected function getFilterableFields() {
        return [
            Question::ID,
            Question::REFERENCE,
            Question::USER_ID,
            Question::TITLE,
            Question::SLUG,
            Question::FEATURED,
            Question::STICKY,
            Question::SOLVED,
            Question::UP_VOTE,
            Question::DOWN_VOTE,
        ];
    }

    /**
     * @return array
     */
    protected function getRelationNames() {

        return [
            Question::RELATION_USER,
            Question::RELATION_TAGS
        ];
    }
}
