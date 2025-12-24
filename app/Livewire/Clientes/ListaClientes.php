<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ListaClientes extends Component
{
    use WithPagination;

    public $filtro = 'nombres_cliente';
    public $buscador = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Cliente::with('clasificacion');

        if ($this->filtro && $this->buscador != '') {
            $query->where($this->filtro, 'like', '%' . $this->buscador . '%');
        }

        $clientes = $query->paginate(10);   

        return view('livewire.clientes.lista-clientes', compact('clientes'));
    }

}
