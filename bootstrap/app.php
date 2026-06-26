<?php

use App\Http\Responses\Fail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $_middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            switch (true) {
                case $e instanceof ValidationException:
                    return Fail::fromException($e, 422, $e->errors())
                        ->toResponse($request);
                case $e instanceof AuthenticationException:
                    return Fail::fromException($e, 401)
                        ->toResponse($request);
                case $e instanceof AuthorizationException:
                    return Fail::fromException($e, 403)
                        ->toResponse($request);
                case $e instanceof HttpExceptionInterface:
                    return Fail::fromException($e, $e->getStatusCode())
                        ->toResponse($request);
                default:
                    return Fail::fromException($e, 500)
                        ->toResponse($request);
            }
        });
    })->create();
