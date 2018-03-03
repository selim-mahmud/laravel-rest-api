<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\QuestionCollection;
use App\Http\Resources\V1\TagCollection;
use App\Question;
use App\Http\Resources\V1\Question as ResourceQuestion;
use App\Http\Resources\V1\User as ResourceUser;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
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
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        Question $question,
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
        return new QuestionCollection($this->getResourceCollection($queryBuilder));
    }

    /**
     * Display the specified resource.
     *
     * @param string $reference
     * @return ResourceQuestion
     */
    public function show(string $reference): ResourceQuestion
    {
        $question = $this->question->findByReferenceOrFail($reference);
        return new ResourceQuestion($this->getSingleResource($question));
    }

    /**
     * @param $reference
     * @return AnswerCollection
     */
    public function getAnswers(string $reference) : AnswerCollection
    {
        /** @var Question $question */
        $question = $this->question->findByReferenceOrFail($reference);
        $question = $this->getRelatedResourceCollection($question, Question::RELATION_ANSWERS);
        return new AnswerCollection($question->answers);
    }

    /**
     * @param $reference
     * @return TagCollection
     */
    public function getTags(string $reference) : TagCollection
    {
        /** @var Question $question */
        $question = $this->question->findByReferenceOrFail($reference);
        $question = $this->getRelatedResourceCollection($question, Question::RELATION_TAGS);
        return new TagCollection($question->tags);
    }

    /**
     * @param $reference
     * @return ResourceUser
     */
    public function getUser(string $reference) : ResourceUser
    {
        /** @var Question $question */
        $question = $this->question->findByReferenceOrFail($reference)->load(Question::RELATION_USER);
        return new ResourceUser($question->user);
    }

    /**
     * @return array
     */
    protected function getFilterableFields()
    {
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
    protected function getSortableFields()
    {
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
    protected function getRelationNames()
    {

        return [
            Question::RELATION_USER,
            Question::RELATION_ANSWERS,
            Question::RELATION_TAGS
        ];
    }
}
