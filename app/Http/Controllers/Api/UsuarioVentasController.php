<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioVentasController extends BaseApiController
{
    /**
     * Crea un usuario con rol ventas asociado al admin autenticado.
     * Rol permitido: admin_motos.
     * Valida: nombre, correo único, contraseña mínima y existencia del rol ventas.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no debe superar 255 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.string' => 'El correo debe ser texto.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.max' => 'El correo no debe superar 255 caracteres.',
            'email.unique' => 'El correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $rolVentas = Role::where('nombre', Role::VENTAS)->first();

        if (! $rolVentas) {
            return $this->notFound('El rol ventas no está configurado');
        }

        $usuario = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'password_hash' => Hash::make($request->input('password')),
            'rol_id' => $rolVentas->id,
            'creado_por' => $request->user()->id,
            'activo' => true,
        ]);

        return $this->ok('Usuario ventas creado correctamente', [
            'usuario' => $usuario->load('role', 'creador'),
        ], 201);
    }
}