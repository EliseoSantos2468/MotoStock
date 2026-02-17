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

    protected $messages = [
        'nombres_cliente.required' => 'El campo nombres es obligatorio.',
        'nombres_cliente.regex' => 'El campo nombres solo debe contener letras y espacios.',
        'apellidos_cliente.required' => 'El campo apellidos es obligatorio.',
        'apellidos_cliente.regex' => 'El campo apellidos solo debe contener letras y espacios.',
        'dui_cliente.required' => 'El campo DUI es obligatorio.',
        'dui_cliente.regex' => 'El DUI debe tener el formato 00000000-0.',
        'dui_cliente.unique' => 'El DUI ya esta registrado.',
        'nit_cliente.required' => 'El campo NIT es obligatorio.',
        'nit_cliente.regex' => 'El NIT debe tener el formato 0000-000000-000.',
        'nit_cliente.unique' => 'El NIT ya esta registrado.',
        'telefono_cliente.required' => 'El campo telefono es obligatorio.',
        'telefono_cliente.regex' => 'El telefono debe tener el formato 7777-7777.',
        'email_cliente.required' => 'El campo correo es obligatorio.',
        'email_cliente.email' => 'El correo no tiene un formato valido.',
        'email_cliente.unique' => 'El correo ya esta registrado.',
        'barrio.required' => 'El campo barrio es obligatorio.',
        'referencias.required' => 'Debe agregar al menos una referencia.',
        'referencias.*.nombre_ref.required' => 'El nombre de la referencia es obligatorio.',
        'referencias.*.nombre_ref.regex' => 'El nombre de referencia solo debe contener letras y espacios.',
        'referencias.*.telefono_ref.required' => 'El telefono de la referencia es obligatorio.',
        'referencias.*.telefono_ref.regex' => 'El telefono de la referencia debe tener el formato 7777-7777.',
        'id_departamento.required' => 'Debe seleccionar un departamento.',
        'id_departamento.exists' => 'El departamento seleccionado no es valido.',
        'id_municipio.required' => 'Debe seleccionar un municipio.',
        'id_municipio.exists' => 'El municipio seleccionado no es valido.',
    ];

    protected $validationAttributes = [
        'nombres_cliente' => 'nombres',
        'apellidos_cliente' => 'apellidos',
        'dui_cliente' => 'DUI',
        'nit_cliente' => 'NIT',
        'telefono_cliente' => 'telefono',
        'email_cliente' => 'correo',
        'barrio' => 'barrio',
        'referencias.*.nombre_ref' => 'nombre de referencia',
        'referencias.*.telefono_ref' => 'telefono de referencia',
        'id_departamento' => 'departamento',
        'id_municipio' => 'municipio',
    ];
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
    public $modalCliente = false;
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

        if($this->modalCliente && empty($this->departamentos)){
            $this->departamentos = Departamento::all();
        }

        $clientes = $query->paginate(10);   

        return view('livewire.clientes.lista-clientes', compact('clientes'));
    }

    public function agregarReferencia(){
        $this->validate([
            'ref_nombre' => 'required|string|max:100|regex:/^[\pL\s]+$/u',
            'ref_telefono' => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
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
        $this->modalCliente = true;
    }

    public function editarCliente($id){
        $this->form = 'editar';
        $this->modalConfirmTitle = '¿editar cliente?';
        $this->modalConfirmContent = '¿desea editar el cliente?';
        $this->editarClienteData($id);
    }

    public function editarClienteData($id){
        $cliente = Cliente::findOrFail($id);

        $this->cliente_id = $id;

        $this->fill($cliente->toArray());

        if ($this->id_departamento) {
            $this->municipios = Municipio::where('departamento_id', $this->id_departamento)->get();
        }

        $this->referencias = $cliente->referencias->map(function($ref){
            return [
                'telefono_ref' => $ref->telefono_ref,
                'nombre_ref' => $ref->nombre_ref,
            ];
        })->toArray();

        $this->modalCliente = true;
    }

    public function abrirConfirmacion() 
    {
        $rules = [
            'nombres_cliente'   => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellidos_cliente' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
                'telefono_cliente'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'barrio'            => 'required|string|max:255',
            'referencias'                 => 'required|array|min:1',
            'referencias.*.nombre_ref'    => 'required|string|max:100|regex:/^[\pL\s]+$/u',
                'referencias.*.telefono_ref'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'id_departamento'   => 'required|exists:departamento,id',
            'id_municipio'      => 'required|exists:municipio,id',
        ];

        if ($this->form === 'crear') {
            $rules['dui_cliente'] = 'required|string|max:10|regex:/^\d{8}-\d$/|unique:cliente,dui_cliente';
            $rules['nit_cliente'] = 'required|string|max:20|regex:/^\d{4}-\d{6}-\d{3}$/|unique:cliente,nit_cliente';
            $rules['email_cliente'] = 'required|email|max:255|unique:cliente,email_cliente';
        }

        $this->validate($rules);

        $this->modalCliente = false;
        $this->modalActualizar = false;

        $this->modalConfirm = true;
    }

    public function cerrarConfirmacion(){

        $this->modalConfirm = false;

        $this->modalCliente = true;
    }

    public function crear(){        
        $validatedData = $this->validate([
        'nombres_cliente'   => 'required|string|max:255|regex:/^[\pL\s]+$/u',
        'apellidos_cliente' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
        'dui_cliente'       => 'required|string|max:10|regex:/^\d{8}-\d$/|unique:cliente,dui_cliente',
                'telefono_cliente'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
        'nit_cliente'       => 'required|string|max:20|regex:/^\d{4}-\d{6}-\d{3}$/|unique:cliente,nit_cliente',
        'email_cliente'     => 'required|email|max:255|unique:cliente,email_cliente',
        'barrio'            => 'required|string|max:255',
        'referencias'                 => 'required|array|min:1',
        'referencias.*.nombre_ref'    => 'required|string|max:100|regex:/^[\pL\s]+$/u',
                'referencias.*.telefono_ref'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
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

    public function editar(){
        
        $validatedData = $this->validate([
            'nombres_cliente'   => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'apellidos_cliente' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
                'telefono_cliente'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'barrio'            => 'required|string|max:255',
            'referencias'                 => 'required|array|min:1',
            'referencias.*.nombre_ref'    => 'required|string|max:100|regex:/^[\pL\s]+$/u',
                'referencias.*.telefono_ref'  => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
            'id_departamento'   => 'required|exists:departamento,id',
            'id_municipio'      => 'required|exists:municipio,id',
        ]);
        
        
        try{
            DB::transaction(function(){
                $cliente = Cliente::findOrFail($this->cliente_id);

                $cliente->update([
                    'nombres_cliente' => $this->nombres_cliente,
                    'apellidos_cliente' => $this->apellidos_cliente,
                    'telefono_cliente' => $this->telefono_cliente,
                    'barrio' => $this->barrio,
                    'id_departamento' => $this->id_departamento,
                    'id_municipio' => $this->id_municipio,
                ]);

                // Borramos físicamente las referencias antiguas y su relación
                $cliente->referencias->each->delete();
                $cliente->referencias()->detach();

                foreach($this->referencias as $ref){
                    $referencia = Referencia::create([
                        'telefono_ref' => $ref['telefono_ref'],
                        'nombre_ref' => $ref['nombre_ref'],
                    ]);
                    $cliente->referencias()->attach($referencia->id);
                }

            });
            $this->cerrarModal();
            $this->dispatch('cliente-editado');
        } catch(\Exception $e){
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
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
            'modalCliente',
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
