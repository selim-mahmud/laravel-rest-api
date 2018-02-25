<?php

namespace App\Http\Controllers\Api;

use App\ApiColumnFilter;
use App\ApiQueryRelation;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class ApiController extends Controller
{

    /**
     * @var ApiRequest $request
     */
    protected $request;

    /**
     * @var ApiColumnFilterHandler $queryFilterHandler
     */
    protected $queryFilterHandler;

    /**
     * @var ApiRelationAdditionHandler $relationAdditionService
     */
    protected $relationAdditionService;

    /**
     * @var ApiRelationFilterHandler $relationHandlerService
     */
    protected $relationHandlerService;

    /**
     * ApiController constructor.
     *
     * @param ApiRequest $request
     * @param ApiColumnFilterHandler $filterHandlerService
     * @param ApiRelationAdditionHandler $relationAdditionService
     * @param ApiRelationFilterHandler $relationHandlerService
     */
    function __construct(
        ApiRequest $request,
        ApiColumnFilterHandler $filterHandlerService,
        ApiRelationAdditionHandler $relationAdditionService,
        ApiRelationFilterHandler $relationHandlerService
    )
    {
        $this->request = $request;
        $this->queryFilterHandler = $filterHandlerService;
        $this->relationAdditionService = $relationAdditionService;
        $this->relationHandlerService = $relationHandlerService;
    }

    /**
     * Generate a list using the pre-populated query builder provided.
     *
     * @param Builder $builder
     * @return Collection|LengthAwarePaginator
     */
    protected function getListCollection(Builder $builder)
    {
        // Add any filters to query builder
        $filters = $this->queryFilterHandler->getCollectionOfFilters();
        if ($filters) {
            /** @var ApiColumnFilter $filter */
            foreach ($filters as $filter) {
                $builder->where(
                    $filter->column,
                    $filter->operator,
                    $filter->value
                );
            }
        }

        // Add any filter based on relations to query builder
        $relations = $this->relationHandlerService->getCollectionOfRelations();

        if ($relations) {

            /** @var ApiQueryRelation $relation */
            foreach ($relations as $relation) {
                $builder->has(
                    $relation->relationName,
                    $relation->relationOperator,
                    $relation->relationValue
                );
            }
        }

        // Load related resource
        $loadRelations = $this->relationAdditionService->getArrayOfRelations();

        if ($loadRelations) {
            foreach ($loadRelations as $loadRelation) {
                $builder->with($loadRelation);
            }
        }

        // get unlimited results
        if ($this->request->unlimitedPaginatedResultsRequested()) {
            return $builder->get();
        }

        return $builder->paginate($this->request->getPaginationLimit());
    }
}
