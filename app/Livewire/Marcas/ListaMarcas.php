<?php

namespace App\Livewire\Marcas;

use App\Models\Marca;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;


class ListaMarcas extends Component
{
    use WithPagination;
    // buscador
    public $buscador = '';
    public $filtro = 'nombre_marca';
    //datos de marcas
    public $marca_id;
    public $nombre_marca = '';
    // modales
    public $modalMarca = false;
    public $modalActualizar = false;
    public $modalConfirm = false;
    public $modalConfirmTitle = '';
    public $modalConfirmContent = '';
    // logica
    public $form = '';


    #[Layout('layouts.app')]
    public function render()
    {
        $query = Marca::query();

        if ($this->filtro && $this->buscador != '') {
            $query->where($this->filtro, 'like', '%' . $this->buscador . '%');
        }

        $marcas = $query->paginate(10);
        return view('livewire.marcas.lista-marcas', compact('marcas'));
    }

    public function cerrarConfirmacion(){

        $this->modalConfirm = false;

        $this->modalMarca = true;
    }

    public function eliminarMarca($id){
        $this->modalConfirmTitle = '¿eliminar marca?';
        $this->modalConfirmContent = '¿desea eliminar la marca?';
        $this->marca_id = $id;
        $this->modalConfirm = true;
    }

    public function delete(){

        try {
            $marca = Marca::findOrFail($this->marca_id); 

            $marca->productos()->detach();

            $marca->delete();

            $this->cerrarModal();
            $this->dispatch('marca-eliminada');
        } catch (\Exception $e) {
            session()->flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function crearMarca(){
        $this->form = 'crear';
        $this->modalConfirmTitle = '¿crear marca?';
        $this->modalConfirmContent = '¿desea crear una nueva marca?';
        $this->modalMarca = true;
    }

    public function editarMarca($id){
        $this->form = 'editar';
        $this->modalConfirmTitle = '¿editar marca?';
        $this->modalConfirmContent = '¿desea editar la marca?';
        $this->editarMarcaData($id);
    }

    public function editarMarcaData($id){
        $marca = Marca::findOrFail($id);

        $this->marca_id = $id;

        $this->fill($marca->toArray());

        $this->modalMarca = true;
    }

    public function editar(){
        $this->validate([
            'nombre_marca' => 'required|string|max:255|unique:marca,nombre_marca',
        ]);

        try {

            $marca = Marca::findOrFail($this->marca_id);

            $marca->update([
                'nombre_marca' => $this->nombre_marca,
            ]);

            $this->cerrarModal();
            $this->dispatch('marca-editada');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function abrirConfirmacion(){
        if($this->marca_id == ''){
            $this->validate([
                'nombre_marca' => 'required|string|max:255|unique:marca,nombre_marca',
            ]);
        }

        $this->modalMarca = false;
        $this->modalActualizar = false;

        $this->modalConfirm = true;
    }

    public function crear(){
        $this->validate([
            'nombre_marca' => 'required|string|max:255|unique:marca,nombre_marca',
        ]);

        try {
            Marca::create([
                'nombre_marca' => $this->nombre_marca,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        $this->cerrarModal();
        $this->dispatch('marca-guardada');
    }

    public function cerrarModal(){
        $this->reset([
            'form',
            'marca_id',
            'nombre_marca',
            'modalMarca',
            'modalActualizar',
            'modalConfirm',
            'modalConfirmTitle',
            'modalConfirmContent',
        ]);
    }
}
