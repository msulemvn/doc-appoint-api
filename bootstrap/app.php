<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*')) {
                throw new AuthenticationException('Unauthenticated.');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'statusCode' => Response::HTTP_UNAUTHORIZED,
                    'status' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                    'errors' => null,
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (ValidationException $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'status' => Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    'errors' => $exception->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $exception->getMessage() ?: 'This action is unauthorized.',
                    'statusCode' => Response::HTTP_FORBIDDEN,
                    'status' => Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    'errors' => null,
                ], Response::HTTP_FORBIDDEN);
            }
        });
    })
    ->create();
