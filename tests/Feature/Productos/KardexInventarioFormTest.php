<?php

use App\Livewire\Productos\ListaProductos;
use App\Models\Marca;
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

function crearUsuarioKardex(string $email): User
{
    return User::create([
        'name' => 'Kardex Test',
        'email' => $email,
        'password' => Hash::make('password'),
    ]);
}

test('abrir confirmacion sin datos muestra todos los errores requeridos', function () {
    $this->actingAs(crearUsuarioKardex('kardex-empty@example.com'));

    Livewire::test(ListaProductos::class)
        ->call('abrirConfirmacion')
        ->assertHasErrors([
            'nombre_producto' => 'required',
            'descripcion_producto' => 'required',
            'marcas_nuevas' => 'required',
        ])
        ->assertSee('El nombre del producto es obligatorio.')
        ->assertSee('Debes agregar al menos una marca al producto.');
});

test('nombre y descripcion demasiado cortos son rechazados', function () {
    $this->actingAs(crearUsuarioKardex('kardex-short@example.com'));

    Livewire::test(ListaProductos::class)
        ->set('nombre_producto', 'A')
        ->set('descripcion_producto', 'abcd')
        ->call('abrirConfirmacion')
        ->assertHasErrors([
            'nombre_producto' => 'min',
            'descripcion_producto' => 'min',
        ]);
});

test('agregarMarca exige jerarquia Publico >= Taller >= Mayoreo', function () {
    $user = crearUsuarioKardex('kardex-jerarquia@example.com');
    $this->actingAs($user);

    $marca = Marca::create(['nombre_marca' => 'Marca Jerarquia', 'user_id' => $user->id]);

    // Taller (30) mayor que Publico (20) -> debe fallar
    Livewire::test(ListaProductos::class)
        ->set('idMarca', $marca->id)
        ->set('cantidadMarca', 5)
        ->set('PrecioCosto', 100)
        ->set('PorcentajePublico', 20)
        ->set('PorcentajeTaller', 30)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->assertHasErrors(['PorcentajePublico']);
});

test('agregarMarca no permite seleccionar una marca de otro usuario', function () {
    $otroUsuario = crearUsuarioKardex('kardex-otro@example.com');
    $marcaAjena = Marca::create(['nombre_marca' => 'Marca Ajena', 'user_id' => $otroUsuario->id]);

    $this->actingAs(crearUsuarioKardex('kardex-propio@example.com'));

    Livewire::test(ListaProductos::class)
        ->set('idMarca', $marcaAjena->id)
        ->set('cantidadMarca', 5)
        ->set('PrecioCosto', 100)
        ->set('PorcentajePublico', 30)
        ->set('PorcentajeTaller', 20)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->assertHasErrors(['idMarca']);
});

test('agregarMarca calcula precios y los agrega a la lista cuando todo es valido', function () {
    $user = crearUsuarioKardex('kardex-ok@example.com');
    $this->actingAs($user);

    $marca = Marca::create(['nombre_marca' => 'Marca Valida', 'user_id' => $user->id]);

    $component = Livewire::test(ListaProductos::class)
        ->set('idMarca', $marca->id)
        ->set('cantidadMarca', 5)
        ->set('cantidadMayoreo', 3)
        ->set('PrecioCosto', 100)
        ->set('PorcentajePublico', 30)
        ->set('PorcentajeTaller', 20)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->assertHasNoErrors();

    $marcasNuevas = $component->get('marcas_nuevas');

    expect($marcasNuevas)->toHaveCount(1);
    expect((float) $marcasNuevas[0]['PrecioC'])->toBe(130.0);
    expect((float) $marcasNuevas[0]['PrecioM'])->toBe(110.0);
    expect((float) $marcasNuevas[0]['PrecioT'])->toBe(120.0);
});

test('producto se crea correctamente con una marca valida', function () {
    $user = crearUsuarioKardex('kardex-crear@example.com');
    $this->actingAs($user);

    $marca = Marca::create(['nombre_marca' => 'Marca Crear', 'user_id' => $user->id]);

    Livewire::test(ListaProductos::class)
        ->set('nombre_producto', 'Llanta Kardex')
        ->set('descripcion_producto', 'Producto de prueba para el kardex')
        ->set('idMarca', $marca->id)
        ->set('cantidadMarca', 5)
        ->set('cantidadMayoreo', 3)
        ->set('PrecioCosto', 100)
        ->set('PorcentajePublico', 30)
        ->set('PorcentajeTaller', 20)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->call('abrirConfirmacion')
        ->assertSet('modalConfirm', true)
        ->call('crear')
        ->assertDispatched('producto-creado');

    $this->assertDatabaseHas('producto', [
        'nombre_producto' => 'Llanta Kardex',
        'user_id' => $user->id,
    ]);
});

test('la unicidad de nombre_producto está aislada por usuario (multi-tenant)', function () {
    $userA = crearUsuarioKardex('kardex-tenant-a@example.com');
    $userB = crearUsuarioKardex('kardex-tenant-b@example.com');

    $this->actingAs($userA);
    $marcaA = Marca::create(['nombre_marca' => 'Marca A', 'user_id' => $userA->id]);

    Livewire::test(ListaProductos::class)
        ->set('nombre_producto', 'Producto Compartido')
        ->set('descripcion_producto', 'Descripcion valida de prueba')
        ->set('idMarca', $marcaA->id)
        ->set('cantidadMarca', 5)
        ->set('cantidadMayoreo', 3)
        ->set('PrecioCosto', 100)
        ->set('PorcentajePublico', 30)
        ->set('PorcentajeTaller', 20)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->call('abrirConfirmacion')
        ->call('crear');

    $this->actingAs($userB);
    $marcaB = Marca::create(['nombre_marca' => 'Marca B', 'user_id' => $userB->id]);

    // El usuario B crea un producto con el MISMO nombre. En un sistema
    // multi-tenant esto debe permitirse: cada usuario tiene su propio
    // catálogo, igual que ocurre con las marcas
    // (Rule::exists('marca','id')->where('user_id', ...)).
    Livewire::test(ListaProductos::class)
        ->set('nombre_producto', 'Producto Compartido')
        ->set('descripcion_producto', 'Otra descripcion valida')
        ->set('idMarca', $marcaB->id)
        ->set('cantidadMarca', 2)
        ->set('cantidadMayoreo', 3)
        ->set('PrecioCosto', 50)
        ->set('PorcentajePublico', 30)
        ->set('PorcentajeTaller', 20)
        ->set('PorcentajeMayoreo', 10)
        ->call('agregarMarca')
        ->call('abrirConfirmacion')
        ->assertHasNoErrors()
        ->call('crear')
        ->assertDispatched('producto-creado');

    $this->assertDatabaseHas('producto', [
        'nombre_producto' => 'Producto Compartido',
        'user_id' => $userA->id,
    ]);
    $this->assertDatabaseHas('producto', [
        'nombre_producto' => 'Producto Compartido',
        'user_id' => $userB->id,
    ]);
});
