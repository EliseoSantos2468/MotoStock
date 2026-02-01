<?php

namespace App\Livewire\Productos;

use App\Models\Marca;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class ListaProductos extends Component
{
    use WithPagination;

    // buscador
    public $buscador = '';
    public $filtro = 'nombre_producto';
    //datos de productos
    public $producto_id;
    public $nombre_producto;
    public $descripcion_producto;
    public $marcas_nuevas = [];
    public $idMarca = 0;
    public $cantidadMarca = 0;
    public $PrecioC = 0;
    public $PrecioM = 0;
    // modales
    public $modalProducto = false;
    public $modalActualizar = false;
    public $modalConfirm = false;
    public $modalConfirmTitle = '';
    public $modalConfirmContent = '';
    //logica
    public $form = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Producto::with(['marcas', 'recibos', 'clientes']);

        if ($this->filtro && $this->buscador != '') {
            $query->where($this->filtro, 'like', '%' . $this->buscador . '%');
        }

        $marcas = Marca::all();
        $productos = $query->paginate(10);
        return view('livewire.productos.lista-productos', compact('productos', 'marcas'));
    }

    
    public function abrirConfirmacion() 
    {
        $this->validate([
            'nombre_producto' => 'required|string|max:255',
            'descripcion_producto' => 'required|string|max:300',
            'marcas_nuevas' => 'required|array|min:1',
            'marcas_nuevas.*.idMarca' => 'required|exists:marca,id',
            'marcas_nuevas.*.cantidadMarca' => 'required|integer',
            'marcas_nuevas.*.PrecioC' => 'required|numeric|min:0',
            'marcas_nuevas.*.PrecioM' => 'required|numeric|min:0',
        ], [
            'marcas_nuevas.required' => 'Debes agregar al menos una marca al producto.',
            'marcas_nuevas.*.idMarca.exists' => 'Una de las marcas seleccionadas no es válida.',
        ]);

        $this->modalProducto = false;
        $this->modalActualizar = false;

        $this->modalConfirm = true;
    }

    public function crearProducto(){
        $this->form = 'crear';
        $this->modalConfirmTitle = "¿Crear Producto?";
        $this->modalConfirmContent = "¿Desea Crear un Nuevo Producto?";
        $this->modalProducto = true;
    }

    public function crear(){
        $validateData = $this->validate([
            'nombre_producto' => 'required|string|max:255',
            'descripcion_producto' => 'required|string|max:300',
            'marcas_nuevas' => 'required|array|min:1',
            'marcas_nuevas.*.idMarca' => 'required|exists:marca,id',
            'marcas_nuevas.*.cantidadMarca' => 'required|integer',
            'marcas_nuevas.*.PrecioC' => 'required|numeric|min:0',
            'marcas_nuevas.*.PrecioM' => 'required|numeric|min:0',
        ], [
            'marcas_nuevas.required' => 'Debes agregar al menos una marca al producto.',
            'marcas_nuevas.*.idMarca.exists' => 'Una de las marcas seleccionadas no es válida.',
        ]);

        try {
            DB::transaction(function () {
                $producto = Producto::create([
                    'nombre_producto' => $this->nombre_producto,
                    'descripcion_producto' => $this->descripcion_producto,
                ]);

                $pivoteDatos = [];

                foreach($this->marcas_nuevas as $item){
                    $pivoteDatos[$item['idMarca']]=[
                        'cantidad' => $item['cantidadMarca'],
                        'precio_cliente' => $item['PrecioC'],
                        'precio_mayoreo' => $item['PrecioM'],
                        'venta_producto' => 0
                    ];
                }

                $producto->marcas()->attach($pivoteDatos);

                });
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear: ' . $e->getMessage());
            dd($e->getMessage());
        }

        $this->cerrarModal(); 
        $this->dispatch('producto-creado');
    }

    public function show($id){
        return redirect()->route('ver-producto', ['producto' => $id]);
    }

    public function cerrarConfirmacion(){

        $this->modalConfirm = false;

        $this->modalProducto = true;
    }

    public function eliminarProducto($id){
        $this->modalConfirmTitle = '¿eliminar producto?';
        $this->modalConfirmContent = '¿desea eliminar el producto?';
        $this->producto_id = $id;
        $this->modalConfirm = true;
    }

    public function delete(){

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

    public function editarProducto($id){
        //recolectar datos
        $producto = Producto::with('marcas')->findOrFail($id);
        $this->producto_id = $id;
        $this->nombre_producto = $producto->nombre_producto;
        $this->descripcion_producto = $producto->descripcion_producto;

        //recolectar las marcas
        foreach ($producto->marcas as $marca) {
            $this->marcas_nuevas[] = [
                'idMarca'       => $marca->id,
                'nombreMarca'   => $marca->nombre_marca,
                'cantidadMarca' => $marca->pivot->cantidad,
                'PrecioC'       => $marca->pivot->precio_cliente,
                'PrecioM'       => $marca->pivot->precio_mayoreo,
            ];
        }

        $this->form = 'editar';
        $this->modalConfirmTitle = "Editar Producto?";
        $this->modalConfirmContent = "¿Desea Editar el Producto?";
        $this->modalProducto = true;
    }

    public function editar(){
        $this->validate([
            'nombre_producto' => 'required|string|max:255',
            'descripcion_producto' => 'required|string|max:300',
            'marcas_nuevas' => 'required|array|min:1',
            'marcas_nuevas.*.idMarca' => 'required|exists:marca,id',
            'marcas_nuevas.*.cantidadMarca' => 'required|integer',
            'marcas_nuevas.*.PrecioC' => 'required|numeric|min:0',
            'marcas_nuevas.*.PrecioM' => 'required|numeric|min:0',
        ]);

        try {
            $producto = Producto::with('marcas')->findOrFail($this->producto_id);

            $producto->update([
                'nombre_producto' => $this->nombre_producto,
                'descripcion_producto' => $this->descripcion_producto,
            ]);

            $pivoteDatos = [];

            foreach($this->marcas_nuevas as $item){
                $pivoteDatos[$item['idMarca']] = [
                    'cantidad' => $item['cantidadMarca'],
                    'precio_cliente' => $item['PrecioC'],
                    'precio_mayoreo' => $item['PrecioM'],
                    'venta_producto' => 0
                ];
            }

            $producto->marcas()->sync($pivoteDatos);

            $this->cerrarModal();
            $this->dispatch('producto-actualizado');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo editar: ' . $e->getMessage());
        }
    }

    public function agregarMarca(){
        $this->validate([
            'idMarca' => 'required|integer|exists:marca,id',
            'cantidadMarca' => 'required|integer',
            'PrecioC' => 'required',
            'PrecioM' => 'required',
        ]);

        $marcaInfo = Marca::find($this->idMarca);

        $this->marcas_nuevas[] = [
            'idMarca' => $this->idMarca,
            'nombreMarca' => $marcaInfo->nombre_marca,
            'cantidadMarca' => $this->cantidadMarca,
            'PrecioC' => $this->PrecioC,
            'PrecioM' => $this->PrecioM,
        ];

        $this->reset([
            'idMarca',
            'cantidadMarca',
            'PrecioC',
            'PrecioM',
        ]);
    }

    public function quitarMarca($index){
        if(isset($this->marcas_nuevas[$index])){
            unset($this->marcas_nuevas[$index]);
        }

        $this->marcas_nuevas = array_values($this->marcas_nuevas);
    }

    public function cerrarModal(){
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
            'modalProducto',
            'modalActualizar',
            'modalConfirm',
            'modalConfirmTitle',
            'modalConfirmContent',
        ]);
    }
}
