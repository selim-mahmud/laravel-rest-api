<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ApiRequest;
use App\Repositories\ReferencedModelRepository;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationFilterHandler;
use App\Transformers\Transformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

abstract class ResourceWithParentApiController extends ModelApiController
{
    /** @var ReferencedModelRepository $parentRepository */
    protected $parentRepository;

    /**
     * ModelWithParentApiController constructor.
     *
     * @param ApiRequest $request
     * @param ReferencedModelRepository $repository
     * @param Transformer $transformer
     * @param ReferencedModelRepository $parentRepository
     * @param ApiColumnFilterHandler $queryFilterHandler
     * @param ApiRelationFilterHandler $queryRelationHandler
     */
    function __construct(
        ApiRequest $request,
        ReferencedModelRepository $repository,
        Transformer $transformer,
        ReferencedModelRepository $parentRepository,
        ApiColumnFilterHandler $queryFilterHandler,
        ApiRelationFilterHandler $queryRelationHandler
    ) {
        parent::__construct($request, $repository, $transformer, $queryFilterHandler, $queryRelationHandler);
        $this->parentRepository = $parentRepository;
    }

    /**
     * Get all items of the current type owned by the parent
     *
     * @param string $parentReference
     * @throws ModelNotFoundException when parent model is not found for reference
     * @return JsonResponse
     */
    public function getSiblings(string $parentReference) : JsonResponse {

        $queryBuilder = $this->buildSiblingsQuery($parentReference);

        return $this->getListResponse($queryBuilder);
    }

    /**
     * Get the query builder for the siblings of the current type
     *
     * @param string $parentReference
     * @return Builder
     */
    protected function buildSiblingsQuery(string $parentReference) : Builder {
        return $this->parentRepository->getModel()

            // find the requested parent type
            ->findByReferenceOrFail($parentReference)

            /**
             * The 'hasMany' function is defining a relationship on the parent
             * class 'Company' with the child class 'SolarPanel'.
             *
             * Using the parent of the current item, get all the child items
             * of the same type as the current item.
             *
             * Eg. For a solar panel, get all the solar panels owned by the
             * company that owns the current solar panel.
             */
            ->hasMany($this->repository->getModel()->getMorphClass())
            ->getQuery()

            // eager load the parent objects
            ->with(
                $this->repository->getModel()->getLoadableRelations()
            );
    }
}