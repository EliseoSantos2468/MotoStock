<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Valida el usuario autenticado y verifica que su rol esté permitido.
     * Rol permitido: se define por parámetro en la ruta.
     * Valida: autenticación activa, rol existente y pertenencia a rolesPermitidos.
     */
    public function handle(Request $request, Closure $next, string ...$rolesPermitidos): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            return response()->json([
                'ok' => false,
                'error' => 'No autenticado',
                'codigo' => 401,
            ], 401);
        }

        $nombreRol = $usuario->role?->nombre;

        if (! $nombreRol || ! in_array($nombreRol, $rolesPermitidos, true)) {
            return response()->json([
                'ok' => false,
                'error' => 'Rol sin permiso para esta acción',
                'codigo' => 403,
            ], 403);
        }

        return $next($request);
    }
}