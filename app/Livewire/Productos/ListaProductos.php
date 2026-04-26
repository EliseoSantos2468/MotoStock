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
        'marcas_nuevas.*.PrecioC.required'      => 'El precio de cliente es obligatorio.',
        'marcas_nuevas.*.PrecioC.numeric'       => 'El precio de cliente debe ser numérico.',
        'marcas_nuevas.*.PrecioC.min'           => 'El precio de cliente no puede ser negativo.',
        'marcas_nuevas.*.PrecioM.required'      => 'El precio de mayoreo es obligatorio.',
        'marcas_nuevas.*.PrecioM.numeric'       => 'El precio de mayoreo debe ser numérico.',
        'marcas_nuevas.*.PrecioM.min'           => 'El precio de mayoreo no puede ser negativo.',
        'marcas_nuevas.*.cantidadMayoreo.required' => 'La cantidad mínima de mayoreo es obligatoria.',
        'marcas_nuevas.*.cantidadMayoreo.integer'  => 'La cantidad mínima de mayoreo debe ser un número entero.',
        'marcas_nuevas.*.cantidadMayoreo.min'      => 'La cantidad mínima de mayoreo debe ser al menos 1.',
        'idMarca.required'                      => 'Selecciona una marca.',
        'idMarca.exists'                        => 'La marca seleccionada no es válida.',
        'cantidadMarca.required'                => 'La cantidad es obligatoria.',
        'cantidadMarca.integer'                 => 'La cantidad debe ser un número entero.',
        'cantidadMarca.min'                     => 'La cantidad debe ser al menos 1.',
        'PrecioC.required'                      => 'El precio de cliente es obligatorio.',
        'PrecioC.numeric'                       => 'El precio de cliente debe ser numérico.',
        'PrecioC.min'                           => 'El precio de cliente no puede ser negativo.',
        'PrecioM.required'                      => 'El precio de mayoreo es obligatorio.',
        'PrecioM.numeric'                       => 'El precio de mayoreo debe ser numérico.',
        'PrecioM.min'                           => 'El precio de mayoreo no puede ser negativo.',
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
    public $PrecioC        = 0;
    public $PrecioM        = 0;
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
            'PrecioC',
            'PrecioM',
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
                        'precio_cliente'    => $item['PrecioC'],
                        'precio_mayoreo'    => $item['PrecioM'],
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
                'PrecioC'         => $marca->pivot->precio_cliente,
                'PrecioM'         => $marca->pivot->precio_mayoreo,
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
                    'precio_cliente'   => $item['PrecioC'],
                    'precio_mayoreo'   => $item['PrecioM'],
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
            'PrecioC'         => 'required|numeric|min:0',
            'PrecioM'         => 'required|numeric|min:0',
            'cantidadMayoreo' => 'required|integer|min:1',  // ← NUEVO
        ]);

        // Si la marca ya existe en la lista → actualizar
        foreach ($this->marcas_nuevas as $index => $marcaExistente) {
            if ((int) $marcaExistente['idMarca'] === (int) $this->idMarca) {
                $this->marcas_nuevas[$index]['cantidadMarca']   = (int) $marcaExistente['cantidadMarca'] + (int) $this->cantidadMarca;
                $this->marcas_nuevas[$index]['PrecioC']         = (float) $this->PrecioC;
                $this->marcas_nuevas[$index]['PrecioM']         = (float) $this->PrecioM;
                $this->marcas_nuevas[$index]['cantidadMayoreo'] = (int) $this->cantidadMayoreo; // ← NUEVO

                $this->reset(['idMarca', 'cantidadMarca', 'PrecioC', 'PrecioM', 'cantidadMayoreo']);
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
            'PrecioC'         => $this->PrecioC,
            'PrecioM'         => $this->PrecioM,
        ];

        $this->reset(['idMarca', 'cantidadMarca', 'PrecioC', 'PrecioM', 'cantidadMayoreo']);
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
            'marcas_nuevas.*.cantidadMayoreo'      => ['required', 'integer', 'min:1'],  // ← NUEVO
            'marcas_nuevas.*.PrecioC'              => ['required', 'numeric', 'min:0'],
            'marcas_nuevas.*.PrecioM'              => ['required', 'numeric', 'min:0'],
        ];
    }
}