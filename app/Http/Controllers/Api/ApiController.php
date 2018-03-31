<?php

namespace App\Http\Controllers\Api;

use App\ApiColumnFilter;
use App\ApiQueryRelation;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use Illuminate\Database\Eloquent\Model;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
     * @var ApiRelationFilterHandler $relationHandlerService
     */
    protected $columnSortingHandlerService;

    /**
     * ApiController constructor.
     *
     * @param ApiRequest $request
     * @param ApiColumnFilterHandler $filterHandlerService
     * @param ApiRelationAdditionHandler $relationAdditionService
     * @param ApiRelationFilterHandler $relationHandlerService
     * @param ApiColumnSortingHandler $columnSortingHandlerService
     */
    function __construct(
        ApiRequest $request,
        ApiColumnFilterHandler $filterHandlerService,
        ApiRelationAdditionHandler $relationAdditionService,
        ApiRelationFilterHandler $relationHandlerService,
        ApiColumnSortingHandler $columnSortingHandlerService
    )
    {
        $this->request                     = $request;
        $this->queryFilterHandler          = $filterHandlerService;
        $this->relationAdditionService     = $relationAdditionService;
        $this->relationHandlerService      = $relationHandlerService;
        $this->columnSortingHandlerService = $columnSortingHandlerService;
    }

    /**
     * Generate a list using the pre-populated query builder provided.
     *
     * @param Builder $builder
     * @return Collection|LengthAwarePaginator
     */
    protected function getResourceCollection(Builder $builder)
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

        // Load related resource
        $loadRelations = $this->relationAdditionService->getArrayOfRelations();
        if ($loadRelations) {
            foreach ($loadRelations as $loadRelation) {
                $builder->with($loadRelation);
            }
        }

        // sort resources
        $sortingColumns = $this->columnSortingHandlerService->getArrayOfSortingColumns();
        if ($sortingColumns) {
            foreach ($sortingColumns as $column => $direction) {
                $builder->orderBy($column, $direction);
            }
        } else {
            $builder->latest();
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

        // get unlimited results
        if ($this->request->unlimitedPaginatedResultsRequested()) {
            return $builder->get();
        }

        return $builder->paginate($this->request->getPaginationLimit());
    }

    /**
     * @param Model $model
     * @return Model $model
     */
    protected function getSingleResource(Model $model): Model
    {
        // Load related resource
        $loadRelations = $this->relationAdditionService->getArrayOfRelations();

        if ($loadRelations) {

            // get unlimited results
            if ($this->request->unlimitedPaginatedResultsRequested()) {
                foreach ($loadRelations as $loadRelation) {
                    $model->load(
                        [
                            $loadRelation => function ($q) {
                                $q->latest();
                            }
                        ]
                    );
                }
            } else {

                foreach ($loadRelations as $loadRelation) {
                    $model->load(
                        [
                            $loadRelation => function ($q) {
                                $q->latest()->paginate($this->request->getPaginationLimit());
                            }
                        ]
                    );
                }
            }
        }

        return $model;
    }

    /**
     * @param Model $model
     * @param string $relation
     * @return Model $model
     */
    protected function getRelatedResourceCollection(Model $model, string $relation): Model
    {
        // get unlimited results
        if ($this->request->unlimitedPaginatedResultsRequested()) {
            $model->load(
                [
                    $relation => function ($q) {
                        $q->latest();
                    }
                ]
            );
        } else {

            $model->load(
                [
                    $relation => function ($q) {
                        $q->latest()->paginate($this->request->getPaginationLimit());
                    }
                ]
            );
        }

        return $model;
    }

    /**
     * @param string $successMessage
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function getSuccessResponse(string $successMessage, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return JsonResponse::create([
            'data' => [
                'status' => 'success',
                'successMessage' => $successMessage
            ]
        ], $statusCode);
    }

    /**
     * @param string $failMessage
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function getFailResponse(string $failMessage, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return JsonResponse::create([
            'data' => [
                'status' => 'fail',
                'failMessage' => $failMessage
            ]
        ], $statusCode);
    }
}
