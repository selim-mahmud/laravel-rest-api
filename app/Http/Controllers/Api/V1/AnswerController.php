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
use Illuminate\Http\Request;
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

        $this->answer            = $answer;
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
     * @param string $id
     * @return ResourceAnswer
     */
    public function show(string $id): ResourceAnswer
    {
        $model = $this->answer->findOrFail(decrypt($id));
        return new ResourceAnswer($this->getSingleResource($model));
    }

    /**
     * @param string $id
     * @return ResourceQuestion
     */
    public function getQuestion(string $id): ResourceQuestion
    {
        /** @var Answer $answer */
        $answer = $this->answer->findOrFail(decrypt($id))->load(Answer::RELATION_QUESTION);
        return new ResourceQuestion($answer->question);
    }

    /**
     * @param string $id
     * @return ResourceUser
     */
    public function getUser(string $id): ResourceUser
    {
        /** @var Answer $answer */
        $answer = $this->answer->findOrFail(decrypt($id))->load(Answer::RELATION_USER);
        return new ResourceUser($answer->user);
    }

    /**
     * @param StoreAnswer $request
     * @return JsonResponse
     */
    public function store(StoreAnswer $request): JsonResponse
    {
        $request->merge([ResourceAnswer::USER_ID => decrypt($request->{ResourceAnswer::USER_ID})]);
        $request->merge([ResourceAnswer::QUESTION_ID => decrypt($request->{ResourceAnswer::QUESTION_ID})]);
        $imputs = $this->answerTransformer->transformInputs($request->all());

        try {

            Answer::create($imputs);
            return $this->getSuccessResponse(StatusMessage::RESOURCE_CREATED, Response::HTTP_CREATED);

        } catch (Exception $exception) {

            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            $this->getValiadationRules()
        );
        $jsonValidator->validate();

        $answer = $this->answer->findOrFail(decrypt($id));
        $inputs   = $this->answerTransformer->transformInputs($request->all());
        $answer->fill($inputs);

        if (!$answer->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $answer = $this->answer->findOrFail(decrypt($id));

        if (!$answer->delete()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_DELETED);
    }

    /**
     * @return array
     */
    protected function getValiadationRules(): array
    {
        return [
            ResourceAnswer::QUESTION_ID => 'string',
            ResourceAnswer::USER_ID => 'string',
            ResourceAnswer::DESCRIPTION => 'string|max:65535',
            ResourceAnswer::EXCEPTED => 'boolean',
            ResourceAnswer::UP_VOTE => 'integer',
            ResourceAnswer::DOWN_VOTE => 'integer',
        ];
    }

    /**
     * @return array
     */
    protected function getFilterableFields()
    {
        return [
            Answer::ID,
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
    protected function getSortableFields()
    {
        return [
            Answer::ID,
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
    protected function getRelationNames()
    {

        return [
            Answer::RELATION_USER,
            Answer::RELATION_QUESTION
        ];
    }
}
