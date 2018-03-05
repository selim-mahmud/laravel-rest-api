<?php

namespace App\Http\Controllers\Api\V1;

use App\Answer;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\StoreAnswer;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\Answer as ResourceAnswer;
use App\Http\Resources\V1\Question as ResourceQuestion;
use App\Http\Resources\V1\User as ResourceUser;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\StatusMessage;
use App\Transformers\V1\AnswerTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class AnswerController extends ApiController
{
    /**@var Answer $answer */
    protected $answer;

    /**@var AnswerTransformer $answerTransformer */
    protected $answerTransformer;

    /**
     * AnswerController constructor.
     *
     * @param ApiRequest $request
     * @param Answer $answer
     * @param AnswerTransformer $answerTransformer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        Answer $answer,
        AnswerTransformer $answerTransformer,
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
        $this->answerTransformer = $answerTransformer;
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
     * @param StoreAnswer $request
     * @return JsonResponse
     */
    public function store(StoreAnswer $request): JsonResponse
    {
        $imputs                      = $this->answerTransformer->transformInputs($request->all());
        $imputs[Answer::REFERENCE] = $this->answer->generateUniqueReference();

        try {

            Answer::create($imputs);
            return $this->getSuccessResponse(StatusMessage::RESOURCE_CREATED, Response::HTTP_CREATED);

        } catch (Exception $exception) {

            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

    }

    /**
     * @param Request $request
     * @param $reference
     * @return JsonResponse
     */
    public function update(Request $request, $reference): JsonResponse
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            $this->getValiadationRules()
        );
        $jsonValidator->validate();

        $answer = $this->answer->findByReferenceOrFail($reference);
        $data     = $this->answerTransformer->transformInputs($request->all());
        $answer->fill($data);

        if (!$answer->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @return array
     */
    protected function getValiadationRules(): array
    {
        return [
            ResourceAnswer::QUESTION_ID => 'integer',
            ResourceAnswer::USER_ID => 'integer',
            ResourceAnswer::DESCRIPTION => 'string|max:65535',
            ResourceAnswer::EXCEPTED => 'boolean',
            ResourceAnswer::UP_VOTE => 'boolean',
            ResourceAnswer::DOWN_VOTE => 'boolean',
        ];
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
