<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware for all requests
        $middleware->web([
            // Web-specific middleware
        ]);

        // API middleware group
        $middleware->api([
            \App\Http\Middleware\ResolveStore::class,
            \App\Http\Middleware\RequireStore::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Register custom middlewares with aliases
        $middleware->alias([
            'module' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'customer.only' => \App\Http\Middleware\CustomerOnly::class,
            'api.key' => \App\Http\Middleware\ValidateApiKey::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'tenant' => \App\Http\Middleware\ResolveStore::class,
            'tenant.required' => \App\Http\Middleware\RequireStore::class,
        ]);

        // Configure CORS
        $middleware->web([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'UNAUTHENTICATED',
                        'message' => 'No autenticado. Por favor, inicie sesión para continuar.',
                    ]
                ], 401);
            }
        });
    })->create();
