<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\StoreQuestion;
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
use App\StatusMessage;
use App\Transformers\V1\QuestionTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class QuestionController extends ApiController
{
    /**@var Question $question */
    protected $question;

    /**@var QuestionTransformer $questionTransformer */
    protected $questionTransformer;

    /**
     * QuestionController constructor.
     *
     * @param ApiRequest $request
     * @param Question $question
     * @param QuestionTransformer $questionTransformer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        Question $question,
        QuestionTransformer $questionTransformer,
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
        $this->questionTransformer = $questionTransformer;
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

    public function store(StoreQuestion $request)
    {
        $imputs = $this->questionTransformer->transformInputs($request->all());
        $imputs[Question::SLUG] = str_slug($imputs[Question::TITLE]);
        $imputs[Question::REFERENCE] = $this->question->generateUniqueReference();

        try{

            Question::create($imputs);
            return $this->getSuccessResponse(StatusMessage::RESOURCE_CREATED, Response::HTTP_CREATED);

        }catch(Exception $exception){

            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

    }

    public function update(StoreQuestion $request, $reference)
    {
        $question = $this->question->findByReferenceOrFail($reference);

        if(!$question->update($this->questionTransformer->transformInputs($request->all()))){
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
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
