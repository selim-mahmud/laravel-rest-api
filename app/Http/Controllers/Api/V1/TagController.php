<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Resources\V1\TagCollection;
use App\Http\Resources\V1\Tag as ResourceTag;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\Tag;

class TagController extends ApiController
{
    /**
     * @var Tag $tag
     */
    protected $tag;

    /**
     * TagController constructor.
     *
     * @param ApiRequest $request
     * @param Tag $tag
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     */
    public function __construct(
        ApiRequest $request,
        Tag $tag,
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

        $this->tag = $tag;
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
    protected function getRelationNames()
    {

        return [
            Tag::RELATION_QUESTIONS,
        ];
    }
}
