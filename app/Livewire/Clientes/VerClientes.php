<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\Attributes\Layout;

class VerClientes extends Component
{
    public Cliente $cliente;

    public function mount(Cliente $cliente){
        $this->cliente = $cliente;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clientes.ver-clientes');
    }
}
