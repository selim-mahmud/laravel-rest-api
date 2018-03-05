<?php

namespace App\Exceptions;

use App\StatusMessage;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return JsonResponse
     */
    public function render($request, Exception $exception) : JsonResponse
    {
        switch (get_class($exception)) {

            case NotFoundHttpException::class:
            case ModelNotFoundException::class:
                return $this->getFailResponse(StatusMessage::RESOURCE_NOT_FOUND, Response::HTTP_NOT_FOUND);

            case MaintenanceModeException::class:
                return $this->getFailResponse(StatusMessage::MAINTENANCE_MODE, Response::HTTP_SERVICE_UNAVAILABLE);

            case ValidationException::class:
                return $this->getFailResponse(StatusMessage::VALIDATION_ERROR, Response::HTTP_BAD_REQUEST);

            default:
                return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * @param string $failMessage
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function getFailResponse(string $failMessage, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR) : JsonResponse
    {
        return JsonResponse::create([
            'data' => [
                'status' => 'fail',
                'failMessage' => $failMessage
            ]
        ], $statusCode);
    }
}
