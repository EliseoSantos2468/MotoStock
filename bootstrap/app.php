<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verificarRol' => \App\Http\Middleware\VerificarRol::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Uniforma cualquier excepción no controlada en la API al mismo
        // formato {ok:false, error, codigo} que ya usan los controladores
        // (BaseApiController), en vez de dejar pasar el 500 genérico de
        // Laravel (o una traza completa si APP_DEBUG está activo).
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Datos de entrada inválidos',
                    'codigo' => 422,
                    'errores' => $e->errors(),
                ], 422);
            }

            if (
                $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
            ) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Recurso no encontrado',
                    'codigo' => 404,
                ], 404);
            }

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'ok' => false,
                    'error' => 'No autenticado',
                    'codigo' => 401,
                ], 401);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $e->getStatusCode();

                return response()->json([
                    'ok' => false,
                    'error' => $status === 405 ? 'Método no permitido' : 'Solicitud inválida',
                    'codigo' => $status,
                ], $status);
            }

            \Illuminate\Support\Facades\Log::error('Error no controlado en API: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'ok' => false,
                'error' => 'Ocurrió un error inesperado. Inténtalo de nuevo.',
                'codigo' => 500,
            ], 500);
        });
    })->create();
