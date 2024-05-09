<?php

use Illuminate\Http\JsonResponse;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use App\Exceptions\ValidationException;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum config
        $middleware->trustProxies('*');
        $middleware->statefulApi();

        // Throttles config
        $middleware->throttleWithRedis();
        $middleware->web('throttle:web');
        $middleware->api('throttle:api');

        // Customs middlewares
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e) {
            if (empty($e->validator->failed())) {
                return new JsonResponse(['message' => $e->getMessage()], $e->status);
            }
        });
    })
    ->create();
