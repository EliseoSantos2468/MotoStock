<?php

namespace App\Livewire\Recepciones;

use App\Models\FacturaCompra;
use App\Models\Proveedor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListaRecepciones extends Component
{
    use WithPagination;

    public string $buscador         = '';
    public string $filtroProveedor  = '';
    public string $filtroEstado     = '';
    public string $filtroFechaDesde = '';
    public string $filtroFechaHasta = '';

    public bool $modalConfirm  = false;
    public ?int $factura_id    = null;

    #[Layout('layouts.app')]
    public function render()
    {
        $facturas = FacturaCompra::with('proveedor')
            ->when(trim($this->buscador) !== '', function ($q) {
                $search = '%' . strtolower(trim($this->buscador)) . '%';
                $q->whereRaw('LOWER(numero_factura) LIKE ?', [$search]);
            })
            ->when($this->filtroProveedor !== '', fn ($q) => $q->where('proveedor_id', $this->filtroProveedor))
            ->when($this->filtroEstado !== '',    fn ($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroFechaDesde !== '', fn ($q) => $q->whereDate('fecha', '>=', $this->filtroFechaDesde))
            ->when($this->filtroFechaHasta !== '', fn ($q) => $q->whereDate('fecha', '<=', $this->filtroFechaHasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10);

        $proveedores = Proveedor::orderBy('nombre_proveedor')->get();

        return view('livewire.recepciones.lista-recepciones', compact('facturas', 'proveedores'));
    }

    public function confirmarEliminar(int $id): void
    {
        $this->factura_id    = $id;
        $this->modalConfirm  = true;
    }

    public function eliminar(): void
    {
        try {
            $factura = FacturaCompra::findOrFail($this->factura_id);

            if ($factura->estado !== 'pendiente') {
                session()->flash('error', 'Solo se pueden eliminar facturas en estado pendiente.');
                $this->modalConfirm = false;
                return;
            }

            $factura->delete();
            $this->modalConfirm = false;
            $this->factura_id   = null;
            $this->dispatch('factura-eliminada');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['buscador', 'filtroProveedor', 'filtroEstado', 'filtroFechaDesde', 'filtroFechaHasta']);
        $this->resetPage();
    }

    public function updatedBuscador():         void { $this->resetPage(); }
    public function updatedFiltroProveedor():  void { $this->resetPage(); }
    public function updatedFiltroEstado():     void { $this->resetPage(); }
    public function updatedFiltroFechaDesde(): void { $this->resetPage(); }
    public function updatedFiltroFechaHasta(): void { $this->resetPage(); }
}
