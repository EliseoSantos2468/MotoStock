<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ListaClientes extends Component
{
    use WithPagination;

    // Mensajes de validación personalizados
    protected $messages = [
        'nombres_cliente.required' => 'El campo nombres es obligatorio.',
        'nombres_cliente.regex' => 'El campo nombres solo debe contener letras y espacios.',
        'apellidos_cliente.required' => 'El campo apellidos es obligatorio.',
        'apellidos_cliente.regex' => 'El campo apellidos solo debe contener letras y espacios.',
        'dui_cliente.required' => 'El campo DUI es obligatorio.',
        'dui_cliente.regex' => 'El DUI debe tener el formato 00000000-0.',
        'dui_cliente.unique' => 'El DUI ya está registrado.',
        'nit_cliente.required' => 'El campo NIT es obligatorio.',
        'nit_cliente.regex' => 'El NIT debe tener el formato 0000-000000-000-0.',
        'telefono_cliente.required' => 'El campo teléfono es obligatorio.',
        'telefono_cliente.regex' => 'El teléfono debe tener el formato 0000-0000.',
        'email_cliente.required' => 'El campo correo es obligatorio.',
        'email_cliente.email' => 'El correo no tiene un formato válido.',
        'email_cliente.unique' => 'El correo ya está registrado.',
        'barrio.required' => 'El campo barrio es obligatorio.',
    ];

    // buscador
    public $filtro = 'nombres_cliente';
    public $buscador = '';
    
    // datos de clientes
    public $cliente_id;
    public $nombres_cliente, $apellidos_cliente, $dui_cliente, $telefono_cliente;
    public $nit_cliente, $email_cliente, $barrio;
    public $id_departamento = '', $id_municipio = '';

    // colecciones
    public $departamentos = [], $municipios = [];

    // modales y lógica
    public $modalCliente = false, $modalActualizar = false, $modalConfirm = false;
    public $modalConfirmTitle = '', $modalConfirmContent = '', $form = '';

    /**
     * Reglas dinámicas según el estado (Crear/Editar)
     */
    private function reglas($id = null): array
    {
        $rules = [
            'nombres_cliente'   => 'required|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellidos_cliente' => 'required|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'telefono_cliente'  => 'required|regex:/^\d{4}-\d{4}$/',
            'barrio'            => 'required|string|max:255',
            'id_departamento'   => 'required|exists:departamento,id',
            'id_municipio'      => 'required|exists:municipio,id',
        ];

        // DUI Validation (Solo si es nuevo o si decides permitir edición)
        $duiRule = ['required', 'regex:/^\d{8}-?\d{1}$/'];
        $duiDigits = preg_replace('/\D+/', '', (string) $this->dui_cliente);
        if ($duiDigits !== '000000000') {
            $duiRule[] = Rule::unique('cliente', 'dui_cliente')->ignore($id);
        }
        $rules['dui_cliente'] = $duiRule;

        // NIT Validation
        $nitRule = ['required', 'regex:/^\d{4}-\d{6}-\d{3}-\d{1}$/'];
        $rules['nit_cliente'] = $nitRule;

        // Email Validation
        $rules['email_cliente'] = ['required', 'email'];

        return $rules;
    }

    public function updatedIdDepartamento($value)
    {
        $this->municipios = Municipio::where('departamento_id', $value)->get();
        $this->id_municipio = '';
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Cliente::with(['clasificacion', 'referencias']);

        if ($this->buscador != '') {
            $query->where($this->filtro, 'like', '%' . $this->buscador . '%');
        }

        if($this->modalCliente && empty($this->departamentos)){
            $this->departamentos = Departamento::all();
        }

        return view('livewire.clientes.lista-clientes', [
            'clientes' => $query->paginate(10)
        ]);
    }


    public function abrirConfirmacion() 
    {
        // Validamos con las reglas correspondientes al formulario actual
        $this->validate($this->reglas($this->cliente_id));

        $this->modalCliente = false;
        $this->modalConfirm = true;
    }

    public function crear() 
    {        
        $this->validate($this->reglas());
        
        try {
            DB::transaction(function() {
                $cliente = Cliente::create([
                    'nombres_cliente'   => $this->nombres_cliente,
                    'apellidos_cliente' => $this->apellidos_cliente,
                    'dui_cliente'       => $this->dui_cliente,
                    'telefono_cliente'  => $this->telefono_cliente,
                    'nit_cliente'       => $this->nit_cliente,
                    'email_cliente'     => $this->email_cliente,
                    'monto_max'         => 1000.00,
                    'id_clasificacion'  => 3,
                    'barrio'            => $this->barrio,
                    'id_departamento'   => $this->id_departamento,
                    'id_municipio'      => $this->id_municipio,
                ]);

            });

            $this->cerrarModal();
            $this->dispatch('cliente-guardado');
        } catch(\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function editar() 
    {
        $this->validate($this->reglas($this->cliente_id));
        
        try {
            DB::transaction(function() {
                $cliente = Cliente::findOrFail($this->cliente_id);
                $cliente->update([
                    'nombres_cliente'   => $this->nombres_cliente,
                    'apellidos_cliente' => $this->apellidos_cliente,
                    'telefono_cliente'  => $this->telefono_cliente,
                    'barrio'            => $this->barrio,
                    'id_departamento'   => $this->id_departamento,
                    'id_municipio'      => $this->id_municipio,
                ]);

            });
            
            $this->cerrarModal();
            $this->dispatch('cliente-editado');
        } catch(\Exception $e) {
            session()->flash('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // --- Métodos de apertura y cierre ---

    public function crearCliente() {
        $this->resetValidation();
        $this->cerrarModal();
        $this->form = 'crear';
        $this->modalConfirmTitle = '¿Crear cliente?';
        $this->modalConfirmContent = '¿Desea crear un nuevo registro de cliente?';
        $this->modalCliente = true;
    }

    public function editarCliente($id) {
        $this->resetValidation();
        $this->form = 'editar';
        $this->modalConfirmTitle = '¿Editar cliente?';
        $this->modalConfirmContent = '¿Desea guardar los cambios realizados?';
        $this->editarClienteData($id);
    }

    public function editarClienteData($id) {
        $cliente = Cliente::findOrFail($id);
        $this->cliente_id = $id;
        $this->fill($cliente->toArray());

        if ($this->id_departamento) {
            $this->municipios = Municipio::where('departamento_id', $this->id_departamento)->get();
        }

        $this->modalCliente = true;
    }

    public function cerrarModal() {
        $this->reset([
            'form', 'modalCliente', 'modalActualizar', 'modalConfirm', 'cliente_id',
            'id_departamento', 'id_municipio', 'municipios', 'nombres_cliente',
            'apellidos_cliente', 'dui_cliente', 'telefono_cliente', 'nit_cliente',
            'email_cliente', 'barrio'
        ]);
    }

    public function eliminarCliente($cliente_id) {
        $this->cliente_id = $cliente_id;
        $this->modalConfirmTitle = '¿Eliminar cliente?';
        $this->modalConfirmContent = 'Esta acción no se puede deshacer.';
        $this->modalConfirm = true;
    }

    public function delete() {
        $cliente = Cliente::findOrFail($this->cliente_id);
        $cliente->referencias->each->delete();
        $cliente->referencias()->detach();
        $cliente->delete();

        $this->cerrarModal();
        $this->dispatch('cliente-eliminado');
    }

    public function show($id) {
        return redirect()->route('ver-cliente', ['cliente' => $id]);
    }
}