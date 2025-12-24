<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\Attributes\Layout;

class ListaClientes extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clientes.lista-clientes');
    }
}
