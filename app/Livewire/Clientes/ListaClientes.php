<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Referencia;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ListaClientes extends Component
{
    use WithPagination;
    // buscador
    public $filtro = 'nombres_cliente';
    public $buscador = '';
    //datos de clientes
    public $cliente_id;
    public $nombres_cliente;
    public $apellidos_cliente;
    public $dui_cliente;
    public $telefono_cliente;
    public $nit_cliente;
    public $email_cliente;
    public $barrio;
    public $id_departamento = '';
    public $id_municipio = '';
    public $referencias = [];
    public $ref_nombre, $ref_telefono;
    //departamentos
    public $departamentos = [];
    //municipios
    public $municipios = [];
    // modales
    public $modalCrear = false;
    public $modalActualizar = false;
    public $modalConfirm = false;
    public $modalConfirmTitle = '';
    public $modalConfirmContent = '';
    //logica
    public $form = '';

    public function updatedIdDepartamento($value){
        $this->municipios = Municipio::where('departamento_id', $value)->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Cliente::with(['clasificacion', 'referencias']);

        if ($this->filtro && $this->buscador != '') {
            $query->where($this->filtro, 'like', '%' . $this->buscador . '%');
        }

        if($this->modalCrear && empty($this->departamentos)){
            $this->departamentos = Departamento::all();
        }

        $clientes = $query->paginate(10);   

        return view('livewire.clientes.lista-clientes', compact('clientes'));
    }

    public function agregarReferencia(){
        $this->validate([
            'ref_nombre' => 'required|string|max:100',
            'ref_telefono' => 'required|string|max:20',
        ]);

        $this->referencias[] = [
            'nombre_ref' => $this->ref_nombre,
            'telefono_ref' => $this->ref_telefono,
        ];

        $this->reset([
            'ref_nombre',
            'ref_telefono',
        ]);
    }

    public function eliminarReferencia($index){
        unset($this->referencias[$index]);
        $this->referencias = array_values($this->referencias);
    }

    public function crearCliente(){
        $this->form = 'crear';
        $this->modalConfirmTitle = '¿crear cliente?';
        $this->modalConfirmContent = '¿desea crear un nuevo cliente?';
        $this->modalCrear = true;
    }

    public function abrirConfirmacion() 
    {
        $this->validate([
            'nombres_cliente'   => 'required|string|max:255',
            'apellidos_cliente' => 'required|string|max:255',
            'dui_cliente'       => 'required|string|max:10|unique:cliente,dui_cliente',
            'telefono_cliente'  => 'required|string|max:15',
            'nit_cliente'       => 'required|string|max:20',
            'email_cliente'     => 'required|email|max:255|unique:cliente,email_cliente',
            'barrio'            => 'required|string|max:255',
            'referencias'                 => 'required|array|min:1',
            'referencias.*.nombre_ref'    => 'required|string|max:100',
            'referencias.*.telefono_ref'  => 'required|string|max:20',
            'id_departamento'   => 'required|exists:departamento,id',
            'id_municipio'      => 'required|exists:municipio,id',
        ]);

        $this->modalCrear = false;
        $this->modalActualizar = false;

        $this->modalConfirm = true;
    }

    public function cerrarConfirmacion(){

        $this->modalConfirm = false;

        if ($this->cliente_id) { 
            $this->modalActualizar = true;
        } else {

            $this->modalCrear = true;
        }
    }

    public function create(){

        $validatedData = $this->validate([
        'nombres_cliente'   => 'required|string|max:255',
        'apellidos_cliente' => 'required|string|max:255',
        'dui_cliente'       => 'required|string|max:10|unique:cliente,dui_cliente',
        'telefono_cliente'  => 'required|string|max:15',
        'nit_cliente'       => 'required|string|max:20',
        'email_cliente'     => 'required|email|max:255|unique:cliente,email_cliente',
        'barrio'            => 'required|string|max:255',
        'referencias'                 => 'required|array|min:1',
        'referencias.*.nombre_ref'    => 'required|string|max:100',
        'referencias.*.telefono_ref'  => 'required|string|max:20',
        'id_departamento'   => 'required|exists:departamento,id',
        'id_municipio'      => 'required|exists:municipio,id',
        ]);
        
        
        try{
            DB::transaction(function(){
                $cliente=Cliente::create([
                    'nombres_cliente' => $this->nombres_cliente,
                    'apellidos_cliente' => $this->apellidos_cliente,
                    'dui_cliente' => $this->dui_cliente,
                    'telefono_cliente' => $this->telefono_cliente,
                    'nit_cliente' => $this->nit_cliente,
                    'email_cliente' => $this->email_cliente,
                    'monto_max' => 1000.00,
                    'id_clasificacion' => 3,
                    'barrio' => $this->barrio,
                    'id_departamento' => $this->id_departamento,
                    'id_municipio' => $this->id_municipio,
                ]);

                foreach($this->referencias as $ref){
                    $referencia = Referencia::create([
                        'telefono_ref' => $ref['telefono_ref'],
                        'nombre_ref' => $ref['nombre_ref'],
                    ]);
                    $cliente->referencias()->attach($referencia->id);
                }

            });
        } catch(\Exception $e){
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        $this->cerrarModal();
        $this->dispatch('cliente-guardado');
    }

    public function eliminarCliente($cliente_id){
        $this->modalConfirmTitle = 'eliminar cliente?';
        $this->modalConfirmContent = '¿desea eliminar el cliente?';
        $this->cliente_id = $cliente_id;
        $this->modalConfirm = true;
    }

    public function delete(){
        $cliente = Cliente::findOrFail($this->cliente_id);

        // eliminar referencias
        $cliente->referencias->each(function ($referencia) {
            $referencia->delete(); 
        });

        // eliminar union 
        $cliente->referencias()->detach();

        //eliminar cliente
        $cliente->delete();

        $this->cerrarModal();
        $this->dispatch('cliente-eliminado');
    }

    public function show($id){
        return redirect()->route('ver-cliente', ['cliente' => $id]);
    }

    public function cerrarModal(){
        $this->reset([
            'form',
            'modalCrear',
            'modalActualizar',
            'modalConfirm',
            'modalConfirmTitle',
            'modalConfirmContent',
            'cliente_id',
            'id_departamento',
            'id_municipio',
            'departamentos',
            'municipios',
            'nombres_cliente',
            'apellidos_cliente',
            'dui_cliente',
            'telefono_cliente',
            'nit_cliente',
            'email_cliente',
            'barrio',
            'referencias',
        ]);
    }

}
