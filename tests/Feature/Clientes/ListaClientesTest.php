<?php

use App\Livewire\Clientes\ListaClientes;
use App\Models\Clasificacion;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    Artisan::call('migrate:fresh', [
        '--force' => true,
    ]);
});

test('validaciones de cliente muestran mensajes y bloquean el guardado incompleto', function () {
    $this->actingAs(User::create([
        'name' => 'Admin Test',
        'email' => 'admin-test@example.com',
        'password' => Hash::make('password'),
    ]));

    Livewire::test(ListaClientes::class)
        ->call('abrirConfirmacion')
        ->assertHasErrors([
            'nombres_cliente',
            'apellidos_cliente',
            'dui_cliente',
            'telefono_cliente',
            'email_cliente',
            'barrio',
            'id_departamento',
            'id_municipio',
        ])
        ->assertSee('El campo nombres es obligatorio.')
        ->assertSee('El campo DUI es obligatorio.');
});

test('cliente se puede guardar correctamente sin nit', function () {
    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin-save@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $departamento = Departamento::create([
        'nombre_departamento' => 'San Salvador',
    ]);

    $municipio = Municipio::create([
        'nombre_municipio' => 'San Salvador',
        'departamento_id' => $departamento->id,
    ]);

    Clasificacion::create([
        'nombre_clasificacion' => 'Nuevo',
        'user_id' => $user->id,
    ]);

    Livewire::test(ListaClientes::class)
        ->set('nombres_cliente', 'Juan')
        ->set('apellidos_cliente', 'Pérez')
        ->set('dui_cliente', '12345678-9')
        ->set('telefono_cliente', '7123-4567')
        ->set('email_cliente', 'juan@example.com')
        ->set('barrio', 'Centro')
        ->set('id_departamento', $departamento->id)
        ->set('id_municipio', $municipio->id)
        ->call('abrirConfirmacion')
        ->assertSet('modalConfirm', true)
        ->call('crear')
        ->assertDispatched('cliente-guardado');

    $this->assertDatabaseHas('cliente', [
        'nombres_cliente' => 'Juan',
        'apellidos_cliente' => 'Pérez',
        'dui_cliente' => '12345678-9',
        'telefono_cliente' => '7123-4567',
        'email_cliente' => 'juan@example.com',
        'barrio' => 'Centro',
        'id_departamento' => $departamento->id,
        'id_municipio' => $municipio->id,
        'user_id' => $user->id,
    ]);
});