<?php
namespace App\Livewire\Configuracion;

use Livewire\Component;
use App\Models\Configuracion;
use Livewire\Attributes\Layout;

class Ajustes extends Component
{
    public $color_primario = '#000000';
    public $color_secundario = '#ffffff';
    public $correo_empresa = '';
    
    public $modalConfirmacion = false;

    #[Layout('layouts.app')]
    public function mount()
    {
        $config = Configuracion::first();

        if ($config) {
            $this->color_primario = $config->color_primario;
            $this->color_secundario = $config->color_secundario;
            $this->correo_empresa = $config->correo_empresa;
        }
    }

    public function confirmarGuardado()
    {
        $this->validate([
            'color_primario' => 'required',
            'color_secundario' => 'required',
            'correo_empresa' => 'required|email',
        ]);

        $this->modalConfirmacion = true;
    }

    public function restaurarPorDefecto()
    {
        $this->color_primario = '#475569';
        $this->color_secundario = '#3b82f6';
    }

    public function guardarConfiguracion()
    {
        $config = Configuracion::first();

        if ($config) {
            $config->update([
                'color_primario' => $this->color_primario,
                'color_secundario' => $this->color_secundario,
                'correo_empresa' => $this->correo_empresa,
            ]);
        } else {
            Configuracion::create([
                'color_primario' => $this->color_primario,
                'color_secundario' => $this->color_secundario,
                'correo_empresa' => $this->correo_empresa,
            ]);
        }

        session()->flash('mensaje', '¡Configuración guardada exitosamente!');
        
        return redirect()->route('configuracion');
    }

    public function render()
    {
        return view('livewire.configuracion.ajustes');
    }
}