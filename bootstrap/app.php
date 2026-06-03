<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias JWT middleware
        $middleware->alias([
            'jwt.auth'    => \App\Http\Middleware\JwtMiddleware::class,
            'jwt.refresh' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Global JSON response untuk exception yang tidak tertangkap di controller
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token sudah kadaluarsa.',
                        'data'    => null,
                        'errors'  => null,
                    ], 401);
                }

                if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token tidak valid.',
                        'data'    => null,
                        'errors'  => null,
                    ], 401);
                }

                if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token tidak ditemukan.',
                        'data'    => null,
                        'errors'  => null,
                    ], 401);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. Silakan login terlebih dahulu.',
                        'data'    => null,
                        'errors'  => null,
                    ], 401);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Endpoint tidak ditemukan.',
                        'data'    => null,
                        'errors'  => null,
                    ], 404);
                }

                // JWT package throws UnauthorizedHttpException
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. ' . $e->getMessage(),
                        'data'    => null,
                        'errors'  => null,
                    ], 401);
                }

                // Generic server errors for API
                if (!($e instanceof \Illuminate\Validation\ValidationException)) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->isDebug()
                            ? $e->getMessage()
                            : 'There error in Internal Server',
                        'data'    => null,
                        'errors'  => app()->isDebug() ? $e->getTraceAsString() : null,
                    ], $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                        ? $e->getStatusCode()
                        : 500);
                }
            }
        });
    })->create();
