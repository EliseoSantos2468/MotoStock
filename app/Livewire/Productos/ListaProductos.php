<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;


class ListaProductos extends Component
{
    use WithPagination;

    // buscador
    public $buscador = '';
    public $filtro = 'nombre_producto';
    //datos de productos
    public $producto_id;
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

        $productos = $query->paginate(10);
        return view('livewire.productos.lista-productos', compact('productos'));
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

    public function cerrarModal(){
        $this->reset([
            'form',
            'producto_id',
            'modalProducto',
            'modalActualizar',
            'modalConfirm',
            'modalConfirmTitle',
            'modalConfirmContent',
        ]);
    }
}
