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
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    ) {
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

        $this->question            = $question;
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
    public function getAnswers(string $reference): AnswerCollection
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
    public function getTags(string $reference): TagCollection
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
    public function getUser(string $reference): ResourceUser
    {
        /** @var Question $question */
        $question = $this->question->findByReferenceOrFail($reference)->load(Question::RELATION_USER);
        return new ResourceUser($question->user);
    }

    /**
     * @param StoreQuestion $request
     * @return JsonResponse
     */
    public function store(StoreQuestion $request): JsonResponse
    {
        $inputs                      = $this->questionTransformer->transformInputs($request->all());
        $inputs[Question::REFERENCE] = $this->question->generateUniqueReference();
        $inputs[Question::SLUG]      = str_slug($inputs[Question::TITLE]);

        $tags = $inputs[Question::TAGS];
        unset($inputs[Question::TAGS]);

        try {

            $question = Question::create($inputs);
            $question->tags()->attach($tags);
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
            $this->getUpdateValiadationRules()
        );
        $jsonValidator->validate();

        $question = $this->question->findByReferenceOrFail($reference);
        $data     = $this->questionTransformer->transformInputs($request->all());
        $question->fill($data);

        if (!$question->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @return array
     */
    protected function getCreateValiadationRules(): array
    {
        return [
            ResourceQuestion::USER_ID => 'required|integer',
            ResourceQuestion::TITLE => 'required|string|max:255',
            ResourceQuestion::DESCRIPTION => 'required|string|max:65535',
            ResourceQuestion::FEATURED => 'required|boolean',
            ResourceQuestion::STICKY => 'required|boolean',
            ResourceQuestion::TAGS => 'required|array|tags',
        ];
    }

    /**
     * @return array
     */
    protected function getUpdateValiadationRules(): array
    {
        return [
            ResourceQuestion::USER_ID => 'integer',
            ResourceQuestion::TITLE => 'string|max:255',
            ResourceQuestion::DESCRIPTION => 'string|max:65535',
            ResourceQuestion::FEATURED => 'boolean',
            ResourceQuestion::STICKY => 'boolean',
            ResourceQuestion::SOLVED => 'boolean',
            ResourceQuestion::UP_VOTE => 'boolean',
            ResourceQuestion::DOWN_VOTE => 'boolean',
        ];
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
