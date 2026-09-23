<?php

use App\Livewire\Ventas;
use App\Mail\EnviarReciboMailable;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Recibo;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    Artisan::call('migrate:fresh', [
        '--force' => true,
    ]);

    $this->actingAs(User::create([
        'name' => 'Cajero Test',
        'email' => 'cajero-test@example.com',
        'password' => Hash::make('password'),
    ]));

    $this->producto = Producto::create([
        'nombre_producto' => 'Llanta de prueba',
        'descripcion_producto' => 'Producto para pruebas automatizadas',
    ]);

    $this->marca = Marca::create([
        'nombre_marca' => 'Marca Test',
    ]);

    $this->producto->marcas()->attach($this->marca->id, [
        'cantidad' => 10,
        'cantidad_mayoreo' => 5,
        'precio_costo' => 50,
        'porcentaje_publico' => 30,
        'porcentaje_mayoreo' => 15,
        'porcentaje_taller' => 20,
        'precio_cliente' => 100,
        'precio_mayoreo' => 90,
        'precio_taller' => 95,
        'venta_producto' => 0,
    ]);
});

test('una venta exitosa vacía el carrito y descuenta stock, sin importar el correo', function () {
    Mail::fake();

    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 2)
        ->call('agregarAlCarrito')
        ->set('tipoCliente', 'invitado')
        ->set('nombreInvitado', 'Cliente Mostrador')
        ->set('emailFacturacion', 'cliente-mostrador@example.com')
        ->call('guardarVenta');

    $component->assertHasNoErrors()
        ->assertDontSee('Ocurrió un error al procesar la venta');
    expect($component->get('carrito'))->toBe([]);
    expect((float) $component->get('totalVenta'))->toBe(0.0);

    expect(Recibo::count())->toBe(1);

    $pivot = $this->producto->marcas()->first()->pivot;
    expect((int) $pivot->cantidad)->toBe(8); // 10 - 2 vendidas

    // El correo se ENCOLA (no se envía en el mismo request) para que el
    // cajero no espere a que responda el SMTP de Gmail.
    Mail::assertQueued(EnviarReciboMailable::class);
});

test('si encolar el recibo por correo falla, la venta NO se reporta como error y el carrito igual se vacía', function () {
    // No usamos Mail::fake() aquí: reemplazamos el resultado de Mail::to()->cc()->queue()
    // para forzar una falla real al encolar (ej. conexión a la cola caída) sin tocar servicios externos.
    Mail::shouldReceive('to')->once()->andReturnSelf();
    Mail::shouldReceive('cc')->once()->andReturnSelf();
    Mail::shouldReceive('queue')->once()->andThrow(new \Exception('Cola caída (simulado para prueba)'));

    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 2)
        ->call('agregarAlCarrito')
        ->set('tipoCliente', 'invitado')
        ->set('nombreInvitado', 'Cliente Mostrador')
        ->set('emailFacturacion', 'cliente-mostrador@example.com')
        ->call('guardarVenta');

    // La venta ya se guardó: la vista NO debe mostrar el banner de error,
    // sino el de advertencia (comprobamos lo que el usuario vería en pantalla).
    $component->assertDontSee('Ocurrió un error al procesar la venta')
        ->assertSee('se registró correctamente, pero no se pudo enviar el recibo por correo');

    // El carrito se vació igual, para que el cajero no repita el cobro.
    expect($component->get('carrito'))->toBe([]);
    expect((float) $component->get('totalVenta'))->toBe(0.0);

    // Y la venta quedó realmente registrada en la base de datos.
    expect(Recibo::count())->toBe(1);
    $pivot = $this->producto->marcas()->first()->pivot;
    expect((int) $pivot->cantidad)->toBe(8);
});

test('no se puede vender más cantidad de la disponible en stock', function () {
    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 999)
        ->call('agregarAlCarrito');

    $component->assertHasErrors(['cantidadAVender']);
    expect($component->get('carrito'))->toBe([]);
});

test('el tipo de precio se elige explícitamente y se puede cambiar por línea o para todo el ticket', function () {
    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 2)
        ->set('tipoPrecioItem', 'taller')
        ->call('agregarAlCarrito');

    expect((float) $component->get('carrito')[0]['precio'])->toBe(95.0);
    expect((float) $component->get('totalVenta'))->toBe(190.0);

    // Mayoreo elegido aunque no llegue a la cantidad mínima: lo decide el cajero.
    $component->set('carrito.0.tipoPrecio', 'mayoreo');
    expect((float) $component->get('carrito')[0]['precio'])->toBe(90.0);

    $component->set('tipoPrecio', 'cliente');
    expect((float) $component->get('carrito')[0]['precio'])->toBe(100.0);
    expect((float) $component->get('totalVenta'))->toBe(200.0);
});

test('precio manual por debajo del mínimo pide confirmación y luego se agrega igual', function () {
    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 1)
        ->set('tipoPrecioItem', 'manual')
        ->set('precioManual', 80)
        ->call('agregarAlCarrito');

    // Mínimo configurado es 90 (mayoreo): avisa y no agrega todavía.
    expect($component->get('carrito'))->toBe([]);
    expect($component->get('alertaPrecioBajo'))->toContain('90.00');

    $component->call('agregarAlCarrito', true);

    expect((float) $component->get('carrito')[0]['precio'])->toBe(80.0);
    expect($component->get('carrito')[0]['bajoMinimo'])->toBeTrue();

    // Cambiar el precio global no pisa el precio manual.
    $component->set('tipoPrecio', 'taller');
    expect((float) $component->get('carrito')[0]['precio'])->toBe(80.0);
});

test('precio manual por debajo del costo avisa de pérdida; uno normal se agrega directo', function () {
    $component = Livewire::test(Ventas::class)
        ->call('seleccionarProducto', $this->producto->id)
        ->set('marcaSeleccionada', $this->marca->id)
        ->set('cantidadAVender', 1)
        ->set('tipoPrecioItem', 'manual')
        ->set('precioManual', 40)
        ->call('agregarAlCarrito');

    expect($component->get('alertaPrecioBajo'))->toContain('COSTO');

    $component->set('precioManual', 98)->call('agregarAlCarrito');

    expect($component->get('alertaPrecioBajo'))->toBeNull();
    expect((float) $component->get('carrito')[0]['precio'])->toBe(98.0);
    expect($component->get('carrito')[0]['bajoMinimo'])->toBeFalse();
});
