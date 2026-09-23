<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    /**
     * Responde una operación exitosa con el formato estándar.
     * Rol permitido: cualquier endpoint autenticado.
     * Valida: centraliza la forma de devolver payloads correctos.
     */
    protected function ok(string $mensaje, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $data,
            'mensaje' => $mensaje,
        ], $status);
    }

    /**
     * Responde un error estándar con código HTTP explícito.
     * Rol permitido: cualquier endpoint autenticado.
     * Valida: uniforma el formato de salida para errores funcionales.
     */
    protected function fail(string $error, int $codigo, int $status, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'ok' => false,
            'error' => $error,
            'codigo' => $codigo,
        ], $extra), $status);
    }

    /**
     * Responde errores de validación en el formato estándar.
     * Rol permitido: cualquier endpoint que reciba datos de entrada.
     * Valida: convierte la bolsa de errores del validator en una respuesta homogénea.
     */
    protected function validationError(array $errors, string $mensaje = 'Datos de entrada inválidos'): JsonResponse
    {
        return $this->fail($mensaje, 422, 422, ['errores' => $errors]);
    }

    /**
     * Responde un recurso no encontrado.
     * Rol permitido: cualquier endpoint autenticado.
     * Valida: informa que el identificador consultado no existe.
     */
    protected function notFound(string $error = 'Recurso no encontrado'): JsonResponse
    {
        return $this->fail($error, 404, 404);
    }

    /**
     * Responde un acceso denegado por rol.
     * Rol permitido: cualquier endpoint autenticado.
     * Valida: la acción solicitada no está autorizada para el rol actual.
     */
    protected function forbidden(string $error = 'Rol sin permiso para esta acción'): JsonResponse
    {
        return $this->fail($error, 403, 403);
    }

    /**
     * Responde un conflicto de stock o estado.
     * Rol permitido: operaciones de inventario y préstamos.
     * Valida: stock insuficiente, estado inválido o estado del recurso inconsistente.
     */
    protected function conflict(string $error = 'Stock insuficiente o estado inválido para la operación'): JsonResponse
    {
        return $this->fail($error, 409, 409);
    }
}