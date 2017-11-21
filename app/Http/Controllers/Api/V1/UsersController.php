<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;

class UsersController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse if validation fails
     */
    public function index() : JsonResponse
    {
        dd('Yes');
    }

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $reference
     * @return JsonResponse
     */
    public function edit($reference): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $reference
     * @return JsonResponse
     */
    public function update(Request $request, $reference): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $reference
     * @return JsonResponse
     */
    public function destroy($reference): JsonResponse
    {
        //
    }

}
