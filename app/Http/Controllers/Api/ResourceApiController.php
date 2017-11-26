<?php

namespace App\Http\Controllers\Api;

use App\ApiColumnFilter;
use App\ApiQueryRelation;
use App\ReferencedModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

abstract class ResourceApiController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse if validation fails
     */
    public function index() : JsonResponse
    {
        $queryBuilder = $this->repository->getModel()->withRelations();

        return $this->getListResponse($queryBuilder);
    }

    /**
     * Generate a list using the pre-populated query builder provided.
     *
     * @param Builder $builder
     * @return JsonResponse
     */
    protected function getListResponse(Builder $builder) : JsonResponse {

        // Add any filters to query builder
        $filters = $this->queryFilterHandler->getCollectionOfFilters();
        if($filters) {
            /** @var ApiColumnFilter $filter */
            foreach($filters as $filter) {
                $builder->where(
                    $filter->column,
                    $filter->operator,
                    $filter->value
                );
            }
        }

        // Add any filter based on relations to query builder
        $relations = $this->relationHandlerService->getCollectionOfRelations();

        if($relations){

            /** @var ApiQueryRelation $relation */
            foreach($relations as $relation) {
                $builder->has(
                    $relation->relationName,
                    $relation->relationOperator,
                    $relation->relationValue
                );
            }
        }

        if($this->request->allFieldsRequested()){

            $this->transformer->setFullTransformation();
        }

        // unlimited results
        if($this->request->unlimitedPaginatedResultsRequested()) {

            $collection = $builder->get();

            return $this->respondWithCollection($collection);
        }

        $paginatedCollection = $builder->paginate($this->request->getPaginationLimit());

        return $this->respondWithPagination($paginatedCollection);
    }

//    /**
//     * Show the form for creating a new resource.
//     *
//     * @return JsonResponse
//     */
//    public function create() : JsonResponse
//    {
//        //
//    }

//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param Request  $request
//     * @return JsonResponse
//     */
//    public function store(Request $request) : JsonResponse
//    {
//        //
//    }

    /**
     * Display the specified resource.
     *
     * @param string $reference
     * @throws ModelNotFoundException when model is not found for reference
     * @return JsonResponse
     */
    public function show($reference) : JsonResponse
    {
        $this->transformer->setFullTransformation();

        /** @var ReferencedModel $model */
        $model = $this->repository->getModel()
            ->findByReferenceOrFail($reference);

        return $this->respondWithModel($model);
    }

//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param string $reference
//     * @return JsonResponse
//     */
//    public function edit($reference) : JsonResponse
//    {
//        //
//    }

//    /**
//     * Update the specified resource in storage.
//     *
//     * @param Request  $request
//     * @param string $reference
//     * @return JsonResponse
//     */
//    public function update(Request $request, $reference) : JsonResponse
//    {
//        //
//    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param string $reference
//     * @return JsonResponse
//     */
//    public function destroy($reference) : JsonResponse
//    {
//        //
//    }

}