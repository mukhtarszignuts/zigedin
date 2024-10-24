<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
    ];
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ModelNotFoundException) {
                $a = explode('\\', $exception->getModel());
                $model = end($a);
                $ids = implode(', ', $exception->getIds());

                if ($exception->getIds()) {
                    $message = __(":model with id `$ids` not found.", ['model' => $model]);
                } else {
                    $message = __("Data not found");
                }
                return error($message,[],'notfound');
            } elseif ($exception instanceof RequestException) {
                return error($exception->response->json()['message']);
            }elseif ($exception instanceof ConnectionException) {
                return error($exception->getMessage());
            } elseif ($exception instanceof NotFoundHttpException) {
                return error(__("Route not found."),[],'notfound');
            } elseif ($exception instanceof ValidationException) {
                return error($exception->validator->errors()->first(), $exception->errors(), 'validation');
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                return error($exception->getMessage());
            } elseif ($exception instanceof AuthenticationException) {
                return error($exception->getMessage(), [], 'unauthenticated');
            }
        }

        return parent::render($request, $exception);
    }
}
