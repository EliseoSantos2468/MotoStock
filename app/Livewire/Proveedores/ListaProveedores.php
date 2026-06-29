<?php

namespace App\Livewire\Proveedores;

use App\Models\Proveedor;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListaProveedores extends Component
{
    use WithPagination;

    protected $messages = [
        'nombre_proveedor.required' => 'El nombre del proveedor es obligatorio.',
        'nombre_proveedor.string'   => 'El nombre debe ser texto.',
        'nombre_proveedor.min'      => 'El nombre debe tener al menos 2 caracteres.',
        'nombre_proveedor.max'      => 'El nombre no debe superar 255 caracteres.',
        'telefono.max'              => 'El teléfono no debe superar 20 caracteres.',
        'email.email'               => 'El correo no tiene un formato válido.',
        'email.max'                 => 'El correo no debe superar 255 caracteres.',
    ];

    public string $buscador = '';

    public $proveedor_id;
    public string $nombre_proveedor = '';
    public string $telefono = '';
    public string $email = '';

    public bool $modalProveedor  = false;
    public bool $modalConfirm    = false;
    public string $modalConfirmTitle   = '';
    public string $modalConfirmContent = '';
    public string $form = '';

    #[Layout('layouts.app')]
    public function render()
    {
        $proveedores = Proveedor::query()
            ->when(trim($this->buscador) !== '', function ($q) {
                $search = '%' . Str::lower(trim($this->buscador)) . '%';
                $q->whereRaw('LOWER(nombre_proveedor) LIKE ?', [$search]);
            })
            ->paginate(10);

        return view('livewire.proveedores.lista-proveedores', compact('proveedores'));
    }

    public function crearProveedor(): void
    {
        $this->form = 'crear';
        $this->modalProveedor = true;
    }

    public function editarProveedor(int $id): void
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->proveedor_id     = $id;
        $this->nombre_proveedor = $proveedor->nombre_proveedor;
        $this->telefono         = $proveedor->telefono ?? '';
        $this->email            = $proveedor->email ?? '';
        $this->form             = 'editar';
        $this->modalProveedor   = true;
    }

    public function eliminarProveedor(int $id): void
    {
        $this->proveedor_id        = $id;
        $this->form                = '';
        $this->modalConfirmTitle   = '¿Eliminar proveedor?';
        $this->modalConfirmContent = '¿Desea eliminar este proveedor? Esta acción no se puede deshacer.';
        $this->modalConfirm        = true;
    }

    public function abrirConfirmacion(): void
    {
        $this->validate($this->reglas());

        $this->modalProveedor      = false;
        $this->modalConfirmTitle   = $this->form === 'crear' ? '¿Crear proveedor?' : '¿Guardar cambios?';
        $this->modalConfirmContent = $this->form === 'crear'
            ? '¿Desea registrar el nuevo proveedor?'
            : '¿Desea guardar los cambios del proveedor?';
        $this->modalConfirm = true;
    }

    public function crear(): void
    {
        try {
            Proveedor::create([
                'nombre_proveedor' => $this->nombre_proveedor,
                'telefono'         => $this->telefono ?: null,
                'email'            => $this->email ?: null,
            ]);
            $this->cerrarModal();
            $this->dispatch('proveedor-guardado');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function editar(): void
    {
        try {
            Proveedor::findOrFail($this->proveedor_id)->update([
                'nombre_proveedor' => $this->nombre_proveedor,
                'telefono'         => $this->telefono ?: null,
                'email'            => $this->email ?: null,
            ]);
            $this->cerrarModal();
            $this->dispatch('proveedor-editado');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        try {
            Proveedor::findOrFail($this->proveedor_id)->delete();
            $this->cerrarModal();
            $this->dispatch('proveedor-eliminado');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function cerrarConfirmacion(): void
    {
        $this->modalConfirm = false;
        if ($this->form) {
            $this->modalProveedor = true;
        }
    }

    public function cerrarModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'form', 'proveedor_id', 'nombre_proveedor', 'telefono', 'email',
            'modalProveedor', 'modalConfirm', 'modalConfirmTitle', 'modalConfirmContent',
        ]);
    }

    private function reglas(): array
    {
        return [
            'nombre_proveedor' => ['required', 'string', 'min:2', 'max:255'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255'],
        ];
    }
}
