<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait BasicApiResponses {
    /**
     * @var int $statusCode HTTP Status code
     */
    protected $statusCode = JsonResponse::HTTP_OK;

    /**
     * Get the http status code
     *
     * @return int
     */
    protected function getStatusCode() : int {
        return $this->statusCode;
    }

    /**
     * Set the http status code
     *
     * @param int $statusCode
     * @return BasicApiResponses|static
     */
    protected function setStatusCode($statusCode) : self {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond with a page not found
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found!') : JsonResponse {

        return $this->setStatusCode(JsonResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * Generic response
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function respond($data, $headers = []) : JsonResponse {
        return JsonResponse::create($data, $this->getStatusCode(), $headers);
    }

    /**
     * Respond with data
     *
     * @param mixed $data
     * @return JsonResponse
     */
    protected function respondWithData($data) : JsonResponse {
        return $this->respond([
            'data' => $data
        ]);
    }

    /**
     * Respond with an error message
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondWithError(string $message) : JsonResponse {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }
}