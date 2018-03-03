<?php

namespace App\Http\Controllers\Api\V1;

use App\Answer;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\Answer as ResourceAnswer;
use App\Http\Resources\V1\Question as ResourceQuestion;
use App\Http\Resources\V1\User as ResourceUser;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;

class AnswerController extends ApiController
{
    /**
     * @var Answer $answer
     */
    protected $answer;

    /**
     * AnswerController constructor.
     *
     * @param ApiRequest $request
     * @param Answer $answer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        Answer $answer,
        ApiColumnFilterHandler $columnFilterHandler,
        ApiRelationAdditionHandler $relationAdditionHandler,
        ApiRelationFilterHandler $relationFilterHandler,
        ApiColumnSortingHandler $columnSortingHandler
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
            ),
            $columnSortingHandler->setSortableColumns(
                $this->getSortableFields()
            )
        );

        $this->answer = $answer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnswerCollection
     */
    public function index(): AnswerCollection
    {
        $queryBuilder = $this->answer->newQuery();

        return new AnswerCollection($this->getResourceCollection($queryBuilder));
    }

    /**
     * Display the specified resource.
     *
     * @param string $reference
     * @return ResourceAnswer
     */
    public function show($reference) : ResourceAnswer
    {
        $model = $this->answer->findByReferenceOrFail($reference);
        return new ResourceAnswer($this->getSingleResource($model));
    }

    /**
     * @param $reference
     * @return ResourceQuestion
     */
    public function getQuestion(string $reference) : ResourceQuestion
    {
        /** @var Answer $answer */
        $answer = $this->answer->findByReferenceOrFail($reference)->load(Answer::RELATION_QUESTION);
        return new ResourceQuestion($answer->question);
    }

    /**
     * @param $reference
     * @return ResourceUser
     */
    public function getUser(string $reference) : ResourceUser
    {
        /** @var Answer $answer */
        $answer = $this->answer->findByReferenceOrFail($reference)->load(Answer::RELATION_USER);
        return new ResourceUser($answer->user);
    }

    /**
     * @return array
     */
    protected function getFilterableFields() {
        return [
            Answer::ID,
            Answer::REFERENCE,
            Answer::USER_ID,
            Answer::QUESTION_ID,
            Answer::DESCRIPTION,
            Answer::EXCEPTED,
            Answer::UP_VOTE,
            Answer::DOWN_VOTE,
        ];
    }

    /**
     * @return array
     */
    protected function getSortableFields() {
        return [
            Answer::ID,
            Answer::REFERENCE,
            Answer::USER_ID,
            Answer::QUESTION_ID,
            Answer::DESCRIPTION,
            Answer::EXCEPTED,
            Answer::UP_VOTE,
            Answer::DOWN_VOTE,
        ];
    }

    /**
     * @return array
     */
    protected function getRelationNames() {

        return [
            Answer::RELATION_USER,
            Answer::RELATION_QUESTION
        ];
    }
}
