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
     * @param string $id
     * @return ResourceQuestion
     */
    public function show(string $id): ResourceQuestion
    {
        $question = $this->question->findOrFail(decrypt($id));
        return new ResourceQuestion($this->getSingleResource($question));
    }

    /**
     * @param string $id
     * @return AnswerCollection
     */
    public function getAnswers(string $id): AnswerCollection
    {
        /** @var Question $question */
        $question = $this->question->findOrFail(decrypt($id));
        $question = $this->getRelatedResourceCollection($question, Question::RELATION_ANSWERS);
        return new AnswerCollection($question->answers);
    }

    /**
     * @param string $id
     * @return TagCollection
     */
    public function getTags(string $id): TagCollection
    {
        /** @var Question $question */
        $question = $this->question->findOrFail(decrypt($id));
        $question = $this->getRelatedResourceCollection($question, Question::RELATION_TAGS);
        return new TagCollection($question->tags);
    }

    /**
     * @param string $id
     * @return ResourceUser
     */
    public function getUser(string $id): ResourceUser
    {
        /** @var Question $question */
        $question = $this->question->findOrFail(decrypt($id))->load(Question::RELATION_USER);
        return new ResourceUser($question->user);
    }

    /**
     * @param StoreQuestion $request
     * @return JsonResponse
     */
    public function store(StoreQuestion $request): JsonResponse
    {
        $request->merge([ResourceQuestion::USER_ID => decrypt($request->{ResourceQuestion::USER_ID})]);
        $inputs                 = $this->questionTransformer->transformInputs($request->all());
        $inputs[Question::SLUG] = str_slug($inputs[Question::TITLE]);

        $tags = $inputs[Question::TAGS];
        array_map(function($el) { return decrypt($el); }, $tags);
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
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            $this->getUpdateValiadationRules()
        );
        $jsonValidator->validate();

        /** @var Question $question */
        $question = $this->question->findOrFail(decrypt($id));
        $inputs   = $this->questionTransformer->transformInputs($request->all());

        if (isset($inputs[Question::TAGS])) {
            $tags = $inputs[Question::TAGS];
            unset($inputs[Question::TAGS]);
        }

        $question->fill($inputs);

        if (!$question->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        if (isset($tags)) {
            $question->tags()->sync($tags);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $question = $this->question->findOrFail(decrypt($id));

        if (!$question->delete()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_DELETED);
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
            ResourceQuestion::TAGS => 'tags',
        ];
    }

    /**
     * @return array
     */
    protected function getFilterableFields()
    {
        return [
            Question::ID,
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
