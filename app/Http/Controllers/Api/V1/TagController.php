<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\StoreTag;
use App\Http\Resources\V1\QuestionCollection;
use App\Http\Resources\V1\TagCollection;
use App\Http\Resources\V1\Tag as ResourceTag;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\StatusMessage;
use App\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Transformers\V1\TagTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class TagController extends ApiController
{
    /**@var Tag $tag */
    protected $tag;

    /**@var TagTransformer $tagTransformer */
    protected $tagTransformer;

    /**
     * TagController constructor.
     *
     * @param ApiRequest $request
     * @param Tag $tag
     * @param TagTransformer $tagTransformer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        Tag $tag,
        TagTransformer $tagTransformer,
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

        $this->tag = $tag;
        $this->tagTransformer = $tagTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return TagCollection
     */
    public function index(): TagCollection
    {
        $queryBuilder = $this->tag->newQuery();

        return new TagCollection($this->getResourceCollection($queryBuilder));
    }

    /**
     * Display the specified resource.
     *
     * @param string $reference
     * @return ResourceTag
     */
    public function show($reference) : ResourceTag
    {
        $model = $this->tag->findByReferenceOrFail($reference);
        return new ResourceTag($this->getSingleResource($model));
    }

    /**
     * @param $reference
     * @return QuestionCollection
     */
    public function getQuestions(string $reference) : QuestionCollection
    {
        /** @var Tag $tag */
        $tag = $this->tag->findByReferenceOrFail($reference);
        $tag = $this->getRelatedResourceCollection($tag, Tag::RELATION_QUESTIONS);
        return new QuestionCollection($tag->questions);
    }

    /**
     * @param StoreTag $request
     * @return JsonResponse
     */
    public function store(StoreTag $request): JsonResponse
    {
        $imputs                      = $this->tagTransformer->transformInputs($request->all());
        $imputs[Tag::SLUG]      = str_slug($imputs[Tag::NAME]);
        $imputs[Tag::REFERENCE] = $this->tag->generateUniqueReference();

        try {

            Tag::create($imputs);
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

        $question = $this->tag->findByReferenceOrFail($reference);
        $data     = $this->tagTransformer->transformInputs($request->all());
        $question->fill($data);

        if (!$question->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @param $reference
     * @return JsonResponse
     */
    public function destroy($reference): JsonResponse
    {
        $tag = $this->tag->findByReferenceOrFail($reference);

        if (!$tag->delete()) {
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
            ResourceTag::NAME => 'string|max:255',
            ResourceTag::ACTIVE => 'boolean',
        ];
    }

    /**
     * @return array
     */
    protected function getFilterableFields()
    {
        return [
            Tag::ID,
            Tag::REFERENCE,
            Tag::NAME,
            Tag::SLUG,
            Tag::ACTIVE,
        ];
    }

    /**
     * @return array
     */
    protected function getSortableFields()
    {
        return [
            Tag::ID,
            Tag::REFERENCE,
            Tag::NAME,
            Tag::SLUG,
            Tag::ACTIVE,
        ];
    }

    /**
     * @return array
     */
    protected function getRelationNames()
    {

        return [
            Tag::RELATION_QUESTIONS,
        ];
    }
}
