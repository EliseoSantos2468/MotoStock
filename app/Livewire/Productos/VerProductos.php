<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;

class VerProductos extends Component
{
    public Producto $producto;

    public function mount(Producto $producto){
        $this->producto = $producto->load(['marcas', 'recibos', 'clientes']);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.productos.ver-productos');
    }
}
