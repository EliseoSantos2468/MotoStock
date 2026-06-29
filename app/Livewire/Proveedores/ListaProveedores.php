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
        'telefono.regex'            => 'El formato del teléfono no es válido para el país seleccionado.',
        'email.email'               => 'El correo no tiene un formato válido.',
        'email.max'                 => 'El correo no debe superar 255 caracteres.',
    ];

    public string $buscador = '';

    public $proveedor_id;
    public string $nombre_proveedor = '';
    public string $codigo_pais      = 'HN';
    public string $telefono         = '';
    public string $email            = '';

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

        return view('livewire.proveedores.lista-proveedores', [
            'proveedores'         => $proveedores,
            'paises'              => $this->paises(),
            'telefonoPlaceholder' => $this->telefonoPlaceholder(),
            'telefonoFormato'     => $this->telefonoFormato(),
            'telefonoMaxlength'   => $this->telefonoMaxlength(),
        ]);
    }

    public function updatedCodigoPais(): void
    {
        $this->telefono = '';
        $this->resetValidation('telefono');
    }

    public function updatedTelefono(): void
    {
        $ca = ['HN', 'GT', 'SV', 'NI', 'CR', 'PA'];

        if (in_array($this->codigo_pais, $ca)) {
            $digits = preg_replace('/\D/', '', $this->telefono);
            $digits = substr($digits, 0, 8);
            $this->telefono = strlen($digits) > 4
                ? substr($digits, 0, 4) . '-' . substr($digits, 4)
                : $digits;
            return;
        }

        $allowed = match($this->codigo_pais) {
            'MX', 'CO', 'VE', 'AR' => '/[^0-9\-\s]/',
            'US', 'CA'              => '/[^0-9\-\s\(\)]/',
            default                 => '/[^0-9\+\-\s\(\)\.]/',
        };

        $this->telefono = substr(preg_replace($allowed, '', $this->telefono), 0, $this->telefonoMaxlength());
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
        $this->codigo_pais      = $proveedor->codigo_pais ?? 'HN';
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
                'codigo_pais'      => $this->telefono ? $this->codigo_pais : null,
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
                'codigo_pais'      => $this->telefono ? $this->codigo_pais : null,
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
            'form', 'proveedor_id', 'nombre_proveedor', 'codigo_pais', 'telefono', 'email',
            'modalProveedor', 'modalConfirm', 'modalConfirmTitle', 'modalConfirmContent',
        ]);
        $this->codigo_pais = 'HN';
    }

    private function reglas(): array
    {
        $telefonoRules = ['nullable', 'string', 'max:20'];

        if ($this->telefono !== '') {
            $telefonoRules[] = 'regex:' . $this->telefonoRegex();
        }

        return [
            'nombre_proveedor' => ['required', 'string', 'min:2', 'max:255'],
            'telefono'         => $telefonoRules,
            'email'            => ['nullable', 'email', 'max:255'],
        ];
    }

    private function telefonoRegex(): string
    {
        return match($this->codigo_pais) {
            'HN', 'GT', 'SV', 'NI', 'CR', 'PA' => '/^\d{4}-\d{4}$/',
            'MX'       => '/^(\+?52[-\s.]?)?\d{2}[-\s.]?\d{4}[-\s.]?\d{4}$/',
            'US', 'CA' => '/^(\+?1[-\s.]?)?\(?\d{3}\)?[-\s.]?\d{3}[-\s.]?\d{4}$/',
            'CO'       => '/^(\+?57[-\s.]?)?\d{3}[-\s.]?\d{3}[-\s.]?\d{4}$/',
            'VE'       => '/^(\+?58[-\s.]?)?\d{3}[-\s.]?\d{3}[-\s.]?\d{4}$/',
            'AR'       => '/^(\+?54[-\s.]?)?\d{2,4}[-\s.]?\d{4}[-\s.]?\d{4}$/',
            default    => '/^\+?[\d\s\-\(\)\.]{7,20}$/',
        };
    }

    private function telefonoMaxlength(): int
    {
        return match($this->codigo_pais) {
            'HN', 'GT', 'SV', 'NI', 'CR', 'PA' => 9,
            'MX', 'CO', 'VE', 'AR'              => 12,
            'US', 'CA'                           => 14,
            default                              => 20,
        };
    }

    private function telefonoPlaceholder(): string
    {
        return match($this->codigo_pais) {
            'HN', 'GT', 'SV', 'NI', 'CR', 'PA' => '0000-0000',
            'MX'      => '55 1234-5678',
            'US', 'CA' => '(555) 123-4567',
            'CO'      => '321 123-4567',
            'VE'      => '412 123-4567',
            'AR'      => '011 1234-5678',
            default   => '+1 234 567 8900',
        };
    }

    private function telefonoFormato(): string
    {
        return match($this->codigo_pais) {
            'HN'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'GT'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'SV'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'NI'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'CR'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'PA'      => 'Formato: XXXX-XXXX (8 dígitos)',
            'MX'      => 'Formato: XX XXXX-XXXX (10 dígitos)',
            'US', 'CA' => 'Formato: (XXX) XXX-XXXX (10 dígitos)',
            'CO'      => 'Formato: XXX XXX-XXXX (10 dígitos)',
            'VE'      => 'Formato: XXX XXX-XXXX (10 dígitos)',
            'AR'      => 'Formato: XXX XXXX-XXXX (10 dígitos)',
            default   => 'Formato internacional con código de país',
        };
    }

    private function paises(): array
    {
        return [
            'HN'  => 'Honduras',
            'GT'  => 'Guatemala',
            'SV'  => 'El Salvador',
            'NI'  => 'Nicaragua',
            'CR'  => 'Costa Rica',
            'PA'  => 'Panama',
            'MX'  => 'Mexico',
            'US'  => 'EE.UU.',
            'CA'  => 'Canada',
            'CO'  => 'Colombia',
            'VE'  => 'Venezuela',
            'AR'  => 'Argentina',
            'INT' => 'Internacional',
        ];
    }
}
