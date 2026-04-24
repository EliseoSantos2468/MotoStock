<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ListaClientes extends Component
{
    use WithPagination;

    protected $messages = [
        'nombres_cliente.required' => 'El campo nombres es obligatorio.',
        'nombres_cliente.string' => 'El campo nombres debe ser texto.',
        'nombres_cliente.min' => 'El campo nombres debe tener al menos 2 caracteres.',
        'nombres_cliente.max' => 'El campo nombres no debe superar 255 caracteres.',
        'nombres_cliente.regex' => 'El campo nombres solo debe contener letras y espacios.',
        'apellidos_cliente.required' => 'El campo apellidos es obligatorio.',
        'apellidos_cliente.string' => 'El campo apellidos debe ser texto.',
        'apellidos_cliente.min' => 'El campo apellidos debe tener al menos 2 caracteres.',
        'apellidos_cliente.max' => 'El campo apellidos no debe superar 255 caracteres.',
        'apellidos_cliente.regex' => 'El campo apellidos solo debe contener letras y espacios.',
        'dui_cliente.required' => 'El campo DUI es obligatorio.',
        'dui_cliente.string' => 'El DUI debe ser texto.',
        'dui_cliente.regex' => 'El DUI debe tener el formato 00000000-0.',
        'dui_cliente.unique' => 'El DUI ya está registrado.',
        'nit_cliente.required' => 'El campo NIT es obligatorio.',
        'nit_cliente.string' => 'El NIT debe ser texto.',
        'nit_cliente.regex' => 'El NIT debe tener el formato 0000-000000-000-0 o ser 0.',
        'nit_cliente.unique' => 'El NIT ya está registrado.',
        'telefono_cliente.required' => 'El campo teléfono es obligatorio.',
        'telefono_cliente.string' => 'El teléfono debe ser texto.',
        'telefono_cliente.regex' => 'El teléfono debe tener el formato 0000-0000.',
        'email_cliente.required' => 'El campo correo es obligatorio.',
        'email_cliente.string' => 'El correo debe ser texto.',
        'email_cliente.email' => 'El correo no tiene un formato válido.',
        'barrio.required' => 'El campo barrio es obligatorio.',
        'barrio.string' => 'El campo barrio debe ser texto.',
        'barrio.max' => 'El campo barrio no debe superar 255 caracteres.',
        'id_departamento.required' => 'El campo departamento es obligatorio.',
        'id_departamento.exists' => 'El departamento seleccionado no es válido.',
        'id_municipio.required' => 'El campo municipio es obligatorio.',
        'id_municipio.exists' => 'El municipio seleccionado no es válido.',
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

    private function reglas(?int $id = null): array
    {
        $isEditing = $id !== null;

        $duiRules = ['required', 'string', 'regex:/^\d{8}-\d$/'];

        $nitRules = ['required', 'string', 'regex:/^(0|\d{4}-\d{6}-\d{3}-\d)$/'];
        $emailRules = ['required', 'string', 'email', 'max:255'];

        $duiUnique = Rule::unique('cliente', 'dui_cliente');
        $nitUnique = Rule::unique('cliente', 'nit_cliente');

        if ($id !== null) {
            $duiUnique->ignore($id);
            $nitUnique->ignore($id);
        }

        $duiRules[] = $duiUnique;

        $nitEvaluado = trim((string) $this->nit_cliente);
        $nitDigitos = preg_replace('/\D+/', '', $nitEvaluado);
        $esNitGenerico = $nitEvaluado === '0' || $nitDigitos === '00000000000000';

        if (!$esNitGenerico) {
            $nitRules[] = $nitUnique;
        }

        $rules = [
            'nombres_cliente' => 'required|string|min:2|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellidos_cliente' => 'required|string|min:2|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'dui_cliente' => $duiRules,
            'telefono_cliente' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'barrio' => 'required|string|max:255',
            'id_departamento' => 'required|exists:departamento,id',
            'id_municipio' => 'required|exists:municipio,id',
        ];

        if (!$isEditing) {
            $rules['nit_cliente'] = $nitRules;
            $rules['email_cliente'] = $emailRules;
        }

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
        $query = Cliente::with(['clasificacion']);

        if (trim((string) $this->buscador) !== '') {
            $search = '%' . Str::lower(trim((string) $this->buscador)) . '%';

            if ($this->filtro === 'id') {
                $query->where('id', 'like', '%' . trim((string) $this->buscador) . '%');
            } else {
                $column = in_array($this->filtro, ['nombres_cliente', 'dui_cliente'], true) ? $this->filtro : 'nombres_cliente';
                $query->whereRaw('LOWER(' . $column . ') LIKE ?', [$search]);
            }
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
        $this->normalizarCamposDocumento();
        $this->validate($this->reglas($this->cliente_id));

        $this->modalCliente = false;
        $this->modalConfirm = true;
    }

    public function crear() 
    {        
        try {
            DB::transaction(function() {
                Cliente::create([
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
        try {
            DB::transaction(function() {
                $cliente = Cliente::findOrFail($this->cliente_id);
                $cliente->update([
                    'nombres_cliente'   => $this->nombres_cliente,
                    'apellidos_cliente' => $this->apellidos_cliente,
                    'dui_cliente'       => $this->dui_cliente,
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

    public function cerrarConfirmacion(): void
    {
        $this->modalConfirm = false;
        $this->modalCliente = true;
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
        $this->resetValidation();
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

    private function normalizarCamposDocumento(): void
    {
        $dui = preg_replace('/\D+/', '', (string) $this->dui_cliente);
        if (strlen($dui) >= 9) {
            $dui = substr($dui, 0, 9);
            $this->dui_cliente = substr($dui, 0, 8) . '-' . substr($dui, 8, 1);
        }

        $nit = preg_replace('/\D+/', '', (string) $this->nit_cliente);
        if (strlen($nit) >= 14) {
            $nit = substr($nit, 0, 14);
            $this->nit_cliente = substr($nit, 0, 4) . '-' . substr($nit, 4, 6) . '-' . substr($nit, 10, 3) . '-' . substr($nit, 13, 1);
        }

        $telefono = preg_replace('/\D+/', '', (string) $this->telefono_cliente);
        if (strlen($telefono) >= 8) {
            $telefono = substr($telefono, 0, 8);
            $this->telefono_cliente = substr($telefono, 0, 4) . '-' . substr($telefono, 4, 4);
        }
    }
}