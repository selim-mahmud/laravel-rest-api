<?php

namespace App\Traits;

use App\Http\Requests\ApiRequest;

trait BasicApiResources
{
    /** @var  ApiRequest $apiRequest */
    protected $apiRequest;

    public function __construct(
        ApiRequest $apiRequest
)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * check if all fields requested
     *
     * @return bool
     */
    protected function isAll()
    {
        return $this->apiRequest->isAllFieldsRequested();
    }
}
