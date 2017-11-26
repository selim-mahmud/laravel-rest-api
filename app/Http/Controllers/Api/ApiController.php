<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use App\Repositories\ReferencedModelRepository;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\Traits\BasicApiResponses;
use App\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class ApiController extends Controller
{
    use BasicApiResponses;

    /**
     * @var Transformer $transformer
     */
    protected $transformer;

    /**
     * @var ApiRequest $request
     */
    protected $request;

    /**
     * @var ReferencedModelRepository $repository
     */
    protected $repository;

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
     * @param ReferencedModelRepository $repository
     * @param Transformer $transformer
     * @param ApiColumnFilterHandler $filterHandlerService
     * @param ApiRelationAdditionHandler $relationAdditionService
     * @param ApiRelationFilterHandler $relationHandlerService
     */
    function __construct(
        ApiRequest $request,
        ReferencedModelRepository $repository,
        Transformer $transformer,
        ApiColumnFilterHandler $filterHandlerService,
        ApiRelationAdditionHandler $relationAdditionService,
        ApiRelationFilterHandler $relationHandlerService
    ) {
        $this->request = $request;
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->queryFilterHandler = $filterHandlerService;
        $this->relationAdditionService = $relationAdditionService;
        $this->relationHandlerService = $relationHandlerService;

        $fields = $this->request->getFields();

        if($fields) {
            if(is_array($fields)){
                $this->transformer->setCustomTransformation(
                    $fields
                );
            }elseif ($fields === 'all'){

                $this->transformer->setFullTransformation();
            }

        }
    }

    /**
     * Respond with pagination
     *
     * @param LengthAwarePaginator $paginatedCollection
     * @return JsonResponse
     */
    protected function respondWithPagination(LengthAwarePaginator $paginatedCollection) : JsonResponse {

        // append the limit to the urls
        $paginatedCollection->appends('limit', $paginatedCollection->perPage());

        $fields = $this->request->getFields();

        // append the custom fields to the urls
        if($fields) {
            $paginatedCollection->appends(
                http_build_query([
                    'fields' => $fields
                ])
            );
        }

        // append filters to the urls
        if($this->queryFilterHandler->getCollectionOfFilters()) {
            $paginatedCollection->appends(
                'filters',
                $this->queryFilterHandler->getRawArrayFilters()
            );
        }

        return $this->respond(
            $this->transformer->transformPaginatedCollection($paginatedCollection)
        );
    }

    /**
     * Respond with a collection
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    protected function respondWithCollection(Collection $collection) : JsonResponse {

        /** @var Collection $transformedCollection */
        $transformedCollection = $this->transformer->transformCollection($collection);

        return $this->respondWithData($transformedCollection->toArray());
    }

    /**
     * Respond with a model
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function respondWithModel(Model $model) : JsonResponse {
        return $this->respondWithData(
            $this->transformer->transformModel($model)
        );
    }
}
