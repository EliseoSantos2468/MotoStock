<?php

namespace App\Livewire\Productos;

use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ListaProductos extends Component
{
    use WithPagination;

    protected $messages = [
        'nombre_producto.required'              => 'El nombre del producto es obligatorio.',
        'nombre_producto.string'                => 'El nombre del producto debe ser texto.',
        'nombre_producto.min'                   => 'El nombre del producto debe tener al menos 2 caracteres.',
        'nombre_producto.max'                   => 'El nombre del producto no debe superar 255 caracteres.',
        'nombre_producto.unique'                => 'Ya existe un producto con ese nombre.',
        'descripcion_producto.required'         => 'La descripción del producto es obligatoria.',
        'descripcion_producto.string'           => 'La descripción del producto debe ser texto.',
        'descripcion_producto.min'              => 'La descripción del producto debe tener al menos 5 caracteres.',
        'descripcion_producto.max'              => 'La descripción no debe superar 355 caracteres.',
        'marcas_nuevas.required'                => 'Debes agregar al menos una marca al producto.',
        'marcas_nuevas.array'                   => 'La lista de marcas no es válida.',
        'marcas_nuevas.min'                     => 'Debes agregar al menos una marca al producto.',
        'marcas_nuevas.*.idMarca.required'      => 'Selecciona una marca válida.',
        'marcas_nuevas.*.idMarca.exists'        => 'Una de las marcas seleccionadas no es válida.',
        'marcas_nuevas.*.cantidadMarca.required'=> 'La cantidad es obligatoria.',
        'marcas_nuevas.*.cantidadMarca.integer' => 'La cantidad debe ser un número entero.',
        'marcas_nuevas.*.cantidadMarca.min'     => 'La cantidad debe ser al menos 1.',
        'marcas_nuevas.*.PrecioCosto.required'  => 'El precio costo es obligatorio.',
        'marcas_nuevas.*.PrecioCosto.numeric'   => 'El precio costo debe ser numérico.',
        'marcas_nuevas.*.PrecioCosto.min'       => 'El precio costo no puede ser negativo.',
        'marcas_nuevas.*.PorcentajePublico.required' => 'El porcentaje de público es obligatorio.',
        'marcas_nuevas.*.PorcentajePublico.numeric'  => 'El porcentaje de público debe ser numérico.',
        'marcas_nuevas.*.PorcentajePublico.min'      => '⚠️ El porcentaje de público debe ser al menos 5% (evita pérdidas).',
        'marcas_nuevas.*.PorcentajeMayoreo.required' => 'El porcentaje de mayoreo es obligatorio.',
        'marcas_nuevas.*.PorcentajeMayoreo.numeric'  => 'El porcentaje de mayoreo debe ser numérico.',
        'marcas_nuevas.*.PorcentajeMayoreo.min'      => '⚠️ El porcentaje de mayoreo debe ser al menos 5% (evita pérdidas).',
        'marcas_nuevas.*.PorcentajeTaller.required' => 'El porcentaje de taller es obligatorio.',
        'marcas_nuevas.*.PorcentajeTaller.numeric'  => 'El porcentaje de taller debe ser numérico.',
        'marcas_nuevas.*.PorcentajeTaller.min'      => '⚠️ El porcentaje de taller debe ser al menos 5% (evita pérdidas).',
        'marcas_nuevas.*.cantidadMayoreo.required' => 'La cantidad mínima de mayoreo es obligatoria.',
        'marcas_nuevas.*.cantidadMayoreo.integer'  => 'La cantidad mínima de mayoreo debe ser un número entero.',
        'marcas_nuevas.*.cantidadMayoreo.min'      => 'La cantidad mínima de mayoreo debe ser al menos 1.',
        'idMarca.required'                      => 'Selecciona una marca.',
        'idMarca.exists'                        => 'La marca seleccionada no es válida.',
        'cantidadMarca.required'                => 'La cantidad es obligatoria.',
        'cantidadMarca.integer'                 => 'La cantidad debe ser un número entero.',
        'cantidadMarca.min'                     => 'La cantidad debe ser al menos 1.',
        'PrecioCosto.required'                  => 'El precio costo es obligatorio.',
        'PrecioCosto.numeric'                   => 'El precio costo debe ser numérico.',
        'PrecioCosto.min'                       => 'El precio costo no puede ser negativo.',
        'PorcentajePublico.required'            => 'El porcentaje de público es obligatorio.',
        'PorcentajePublico.numeric'             => 'El porcentaje de público debe ser numérico.',
        'PorcentajePublico.min'                 => '⚠️ El porcentaje de público debe ser al menos 5% (evita pérdidas).',
        'PorcentajeMayoreo.required'            => 'El porcentaje de mayoreo es obligatorio.',
        'PorcentajeMayoreo.numeric'             => 'El porcentaje de mayoreo debe ser numérico.',
        'PorcentajeMayoreo.min'                 => '⚠️ El porcentaje de mayoreo debe ser al menos 5% (evita pérdidas).',
        'PorcentajeTaller.required'             => 'El porcentaje de taller es obligatorio.',
        'PorcentajeTaller.numeric'              => 'El porcentaje de taller debe ser numérico.',
        'PorcentajeTaller.min'                  => '⚠️ El porcentaje de taller debe ser al menos 5% (evita pérdidas).',
        'cantidadMayoreo.required'              => 'La cantidad mínima de mayoreo es obligatoria.',
        'cantidadMayoreo.integer'               => 'La cantidad mínima de mayoreo debe ser un número entero.',
        'cantidadMayoreo.min'                   => 'La cantidad mínima de mayoreo debe ser al menos 1.',
    ];

    // buscador
    public $buscador = '';
    public $filtro = 'nombre_producto';

    // datos de productos
    public $producto_id;
    public $nombre_producto;
    public $descripcion_producto;
    public $marcas_nuevas = [];

    // campos del formulario de marca
    public $idMarca        = '';
    public $cantidadMarca  = 0;
    public $PrecioCosto    = 0;
    public $PorcentajePublico = 0;
    public $PorcentajeMayoreo = 0;
    public $PorcentajeTaller = 0;
    public $PrecioC        = 0;
    public $PrecioM        = 0;
    public $PrecioT        = 0;
    public $cantidadMayoreo = 3; // ← NUEVO: default 3

    // modales
    public $modalProducto     = false;
    public $modalActualizar   = false;
    public $modalConfirm      = false;
    public $modalConfirmTitle = '';
    public $modalConfirmContent = '';

    // lógica
    public $form = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Producto::with(['marcas']);

        if ($this->filtro && trim((string) $this->buscador) !== '') {
            if ($this->filtro === 'id') {
                $query->where('id', 'like', '%' . trim((string) $this->buscador) . '%');
            } else {
                $search = '%' . Str::lower(trim((string) $this->buscador)) . '%';
                $query->whereRaw('LOWER(nombre_producto) LIKE ?', [$search]);
            }
        }

        $marcas   = Marca::all();
        $productos = $query->paginate(10);

        return view('livewire.productos.lista-productos', compact('productos', 'marcas'));
    }

    // ──────────────────────────────────────────────
    //  MODALES
    // ──────────────────────────────────────────────

    public function abrirConfirmacion()
    {
        $this->validate($this->reglasProducto($this->producto_id ?: null));

        $this->modalProducto  = false;
        $this->modalActualizar = false;
        $this->modalConfirm   = true;
    }

    public function cerrarConfirmacion()
    {
        $this->modalConfirm  = false;
        $this->modalProducto = true;
    }

    public function cerrarModal()
    {
        $this->resetValidation();
        $this->reset([
            'form',
            'producto_id',
            'nombre_producto',
            'descripcion_producto',
            'marcas_nuevas',
            'idMarca',
            'cantidadMarca',
            'PrecioCosto',
            'PorcentajePublico',
            'PorcentajeMayoreo',
            'PorcentajeTaller',
            'PrecioC',
            'PrecioM',
            'PrecioT',
            'cantidadMayoreo',   // ← NUEVO
            'modalProducto',
            'modalActualizar',
            'modalConfirm',
            'modalConfirmTitle',
            'modalConfirmContent',
        ]);
    }

    // ──────────────────────────────────────────────
    //  CREAR
    // ──────────────────────────────────────────────

    public function crearProducto()
    {
        $this->form               = 'crear';
        $this->modalConfirmTitle  = '¿Crear Producto?';
        $this->modalConfirmContent = '¿Desea Crear un Nuevo Producto?';
        $this->modalProducto      = true;
    }

    public function crear()
    {
        try {
            DB::transaction(function () {
                $producto = Producto::create([
                    'nombre_producto'      => $this->nombre_producto,
                    'descripcion_producto' => $this->descripcion_producto,
                ]);

                $pivoteDatos = [];

                foreach ($this->marcas_nuevas as $item) {
                    $pivoteDatos[$item['idMarca']] = [
                        'cantidad'          => $item['cantidadMarca'],
                        'cantidad_mayoreo'  => $item['cantidadMayoreo'],  // ← NUEVO
                        'precio_costo'      => $item['PrecioCosto'],
                        'porcentaje_publico' => $item['PorcentajePublico'],
                        'porcentaje_mayoreo' => $item['PorcentajeMayoreo'],
                        'porcentaje_taller'  => $item['PorcentajeTaller'],
                        'precio_cliente'    => $item['PrecioC'],
                        'precio_mayoreo'    => $item['PrecioM'],
                        'precio_taller'     => $item['PrecioT'],
                        'venta_producto'    => 0,
                    ];
                }

                $producto->marcas()->attach($pivoteDatos);
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear: ' . $e->getMessage());
        }

        $this->cerrarModal();
        $this->dispatch('producto-creado');
    }

    // ──────────────────────────────────────────────
    //  EDITAR
    // ──────────────────────────────────────────────

    public function editarProducto($id)
    {
        $producto             = Producto::with('marcas')->findOrFail($id);
        $this->producto_id    = $id;
        $this->nombre_producto      = $producto->nombre_producto;
        $this->descripcion_producto = $producto->descripcion_producto;
        $this->marcas_nuevas  = [];

        foreach ($producto->marcas as $marca) {
            $this->marcas_nuevas[] = [
                'idMarca'         => $marca->id,
                'nombreMarca'     => $marca->nombre_marca,
                'cantidadMarca'   => $marca->pivot->cantidad,
                'cantidadMayoreo' => $marca->pivot->cantidad_mayoreo, // ← NUEVO
                'PrecioCosto'     => $marca->pivot->precio_costo ?? $marca->pivot->precio_cliente,
                'PorcentajePublico' => $marca->pivot->porcentaje_publico ?? 0,
                'PorcentajeMayoreo' => $marca->pivot->porcentaje_mayoreo ?? 0,
                'PorcentajeTaller'  => $marca->pivot->porcentaje_taller ?? 0,
                'PrecioC'         => $marca->pivot->precio_cliente,
                'PrecioM'         => $marca->pivot->precio_mayoreo,
                'PrecioT'         => $marca->pivot->precio_taller ?? $marca->pivot->precio_mayoreo,
            ];
        }

        $this->form               = 'editar';
        $this->modalConfirmTitle  = '¿Editar Producto?';
        $this->modalConfirmContent = '¿Desea Editar el Producto?';
        $this->modalProducto      = true;
    }

    public function editar()
    {
        try {
            $producto = Producto::with('marcas')->findOrFail($this->producto_id);

            $producto->update([
                'nombre_producto'      => $this->nombre_producto,
                'descripcion_producto' => $this->descripcion_producto,
            ]);

            $pivoteDatos = [];

            foreach ($this->marcas_nuevas as $item) {
                $pivoteDatos[$item['idMarca']] = [
                    'cantidad'         => $item['cantidadMarca'],
                    'cantidad_mayoreo' => $item['cantidadMayoreo'],  // ← NUEVO
                    'precio_costo'      => $item['PrecioCosto'],
                    'porcentaje_publico' => $item['PorcentajePublico'],
                    'porcentaje_mayoreo' => $item['PorcentajeMayoreo'],
                    'porcentaje_taller'  => $item['PorcentajeTaller'],
                    'precio_cliente'   => $item['PrecioC'],
                    'precio_mayoreo'   => $item['PrecioM'],
                    'precio_taller'     => $item['PrecioT'],
                    'venta_producto'   => 0,
                ];
            }

            $producto->marcas()->sync($pivoteDatos);

            $this->cerrarModal();
            $this->dispatch('producto-actualizado');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo editar: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    //  ELIMINAR
    // ──────────────────────────────────────────────

    public function eliminarProducto($id)
    {
        $this->modalConfirmTitle  = '¿Eliminar producto?';
        $this->modalConfirmContent = '¿Desea eliminar el producto?';
        $this->producto_id        = $id;
        $this->modalConfirm       = true;
    }

    public function delete()
    {
        try {
            $producto = Producto::findOrFail($this->producto_id);

            $producto->marcas()->detach();
            $producto->recibos()->detach();
            $producto->clientes()->detach();
            $producto->delete();

            $this->cerrarModal();
            $this->dispatch('producto-eliminado');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    //  MARCAS
    // ──────────────────────────────────────────────

    public function agregarMarca()
    {
        $this->validate([
            'idMarca' => [
                'required',
                'integer',
                Rule::exists('marca', 'id')->where('user_id', Auth::id()),
            ],
            'cantidadMarca'   => 'required|integer|min:1',
            'PrecioCosto'     => 'required|numeric|min:0',
            'PorcentajePublico' => 'required|numeric|min:5',
            'PorcentajeMayoreo' => 'required|numeric|min:5',
            'PorcentajeTaller'  => 'required|numeric|min:5',
            'cantidadMayoreo' => 'required|integer|min:1',  // ← NUEVO
        ]);

        // ✓ Validar jerarquía: Público >= Mayoreo >= Taller
        if ((float) $this->PorcentajePublico < (float) $this->PorcentajeMayoreo) {
            $this->addError('PorcentajePublico', '❌ El % de Ganancia Público debe ser ≥ al % de Mayoreo. Público está menor.');
            return;
        }

        if ((float) $this->PorcentajeMayoreo < (float) $this->PorcentajeTaller) {
            $this->addError('PorcentajeMayoreo', '❌ El % de Ganancia Mayoreo debe ser ≥ al % de Taller. Mayoreo está menor.');
            return;
        }

        if ((float) $this->PorcentajePublico < (float) $this->PorcentajeTaller) {
            $this->addError('PorcentajeTaller', '❌ El % de Ganancia Taller debe ser ≤ al % de Público.');
            return;
        }

        $precioPublico = $this->calcularPrecioPorPorcentaje((float) $this->PrecioCosto, (float) $this->PorcentajePublico);
        $precioMayoreo = $this->calcularPrecioPorPorcentaje((float) $this->PrecioCosto, (float) $this->PorcentajeMayoreo);
        $precioTaller = $this->calcularPrecioPorPorcentaje((float) $this->PrecioCosto, (float) $this->PorcentajeTaller);

        // Si la marca ya existe en la lista → actualizar
        foreach ($this->marcas_nuevas as $index => $marcaExistente) {
            if ((int) $marcaExistente['idMarca'] === (int) $this->idMarca) {
                $this->marcas_nuevas[$index]['cantidadMarca']   = (int) $marcaExistente['cantidadMarca'] + (int) $this->cantidadMarca;
                $this->marcas_nuevas[$index]['PrecioCosto']     = (float) $this->PrecioCosto;
                $this->marcas_nuevas[$index]['PorcentajePublico'] = (float) $this->PorcentajePublico;
                $this->marcas_nuevas[$index]['PorcentajeMayoreo'] = (float) $this->PorcentajeMayoreo;
                $this->marcas_nuevas[$index]['PorcentajeTaller']  = (float) $this->PorcentajeTaller;
                $this->marcas_nuevas[$index]['PrecioC']         = $precioPublico;
                $this->marcas_nuevas[$index]['PrecioM']         = $precioMayoreo;
                $this->marcas_nuevas[$index]['PrecioT']         = $precioTaller;
                $this->marcas_nuevas[$index]['cantidadMayoreo'] = (int) $this->cantidadMayoreo; // ← NUEVO

                $this->reset(['idMarca', 'cantidadMarca', 'PrecioCosto', 'PorcentajePublico', 'PorcentajeMayoreo', 'PorcentajeTaller', 'PrecioC', 'PrecioM', 'PrecioT', 'cantidadMayoreo']);
                return;
            }
        }

        // Nueva marca
        $marcaInfo = Marca::find($this->idMarca);

        if (!$marcaInfo) {
            $this->addError('idMarca', 'La marca seleccionada no es válida.');
            return;
        }

        $this->marcas_nuevas[] = [
            'idMarca'         => $this->idMarca,
            'nombreMarca'     => $marcaInfo->nombre_marca,
            'cantidadMarca'   => $this->cantidadMarca,
            'cantidadMayoreo' => $this->cantidadMayoreo,  // ← NUEVO
            'PrecioCosto'     => (float) $this->PrecioCosto,
            'PorcentajePublico' => (float) $this->PorcentajePublico,
            'PorcentajeMayoreo' => (float) $this->PorcentajeMayoreo,
            'PorcentajeTaller'  => (float) $this->PorcentajeTaller,
            'PrecioC'         => $precioPublico,
            'PrecioM'         => $precioMayoreo,
            'PrecioT'         => $precioTaller,
        ];

        $this->reset(['idMarca', 'cantidadMarca', 'PrecioCosto', 'PorcentajePublico', 'PorcentajeMayoreo', 'PorcentajeTaller', 'PrecioC', 'PrecioM', 'PrecioT', 'cantidadMayoreo']);
    }

    public function quitarMarca($index)
    {
        if (isset($this->marcas_nuevas[$index])) {
            unset($this->marcas_nuevas[$index]);
        }
        $this->marcas_nuevas = array_values($this->marcas_nuevas);
    }

    // ──────────────────────────────────────────────
    //  SHOW
    // ──────────────────────────────────────────────

    public function show($id)
    {
        return redirect()->route('ver-producto', ['producto' => $id]);
    }

    // ──────────────────────────────────────────────
    //  REGLAS
    // ──────────────────────────────────────────────

    private function reglasProducto(?int $id = null): array
    {
        $uniqueRule = Rule::unique('producto', 'nombre_producto');

        if ($id !== null) {
            $uniqueRule->ignore($id);
        }

        return [
            'nombre_producto'                      => ['required', 'string', 'min:2', 'max:255', $uniqueRule],
            'descripcion_producto'                 => ['required', 'string', 'min:5', 'max:355'],
            'marcas_nuevas'                        => ['required', 'array', 'min:1'],
            'marcas_nuevas.*.idMarca'              => ['required', 'integer', Rule::exists('marca', 'id')->where('user_id', Auth::id())],
            'marcas_nuevas.*.cantidadMarca'        => ['required', 'integer', 'min:1'],
            'marcas_nuevas.*.cantidadMayoreo'      => ['required', 'integer', 'min:1'],
            'marcas_nuevas.*.PrecioCosto'          => ['required', 'numeric', 'min:0'],
            'marcas_nuevas.*.PorcentajePublico'    => ['required', 'numeric', 'min:5'],
            'marcas_nuevas.*.PorcentajeMayoreo'    => ['required', 'numeric', 'min:5'],
            'marcas_nuevas.*.PorcentajeTaller'     => ['required', 'numeric', 'min:5'],
        ];
    }

    private function calcularPrecioPorPorcentaje(float $precioCosto, float $porcentaje): float
    {
        return round($precioCosto * (1 + ($porcentaje / 100)), 2);
    }
}