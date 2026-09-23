<?php

namespace App\Livewire\Marcas;

use App\Models\Marca;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ListaMarcas extends Component
{
    use WithPagination;

    protected $messages = [
        'nombre_marca.required' => 'El nombre de la marca es obligatorio.',
        'nombre_marca.string' => 'El nombre de la marca debe ser texto.',
        'nombre_marca.min' => 'El nombre de la marca debe tener al menos 2 caracteres.',
        'nombre_marca.max' => 'El nombre de la marca no debe superar 255 caracteres.',
        'nombre_marca.unique' => 'Ya existe una marca con ese nombre.',
    ];
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

        if ($this->filtro && trim((string) $this->buscador) !== '') {
            if ($this->filtro === 'id') {
                $query->where('id', 'like', '%' . trim((string) $this->buscador) . '%');
            } else {
                $search = '%' . Str::lower(trim((string) $this->buscador)) . '%';
                $query->whereRaw('LOWER(nombre_marca) LIKE ?', [$search]);
            }
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
        } catch (\Throwable $e) {
            Log::error('Error al eliminar marca #' . $this->marca_id . ': ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'No se pudo eliminar la marca. Puede que esté en uso por algún producto.');
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
        $marca = Marca::find($id);

        if (!$marca) {
            session()->flash('error', 'Esa marca ya no existe. Puede que haya sido eliminada.');
            return;
        }

        $this->marca_id = $id;

        $this->fill($marca->toArray());

        $this->modalMarca = true;
    }

    public function editar(){
        try {

            $marca = Marca::findOrFail($this->marca_id);

            $marca->update([
                'nombre_marca' => $this->nombre_marca,
            ]);

            $this->cerrarModal();
            $this->dispatch('marca-editada');
        } catch (\Throwable $e) {
            Log::error('Error al editar marca #' . $this->marca_id . ': ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'No se pudo guardar la marca. Inténtalo de nuevo.');
        }
    }

    public function abrirConfirmacion(){
        $this->validate($this->reglas($this->marca_id ?: null));

        $this->modalMarca = false;
        $this->modalActualizar = false;

        $this->modalConfirm = true;
    }

    public function crear(){
        try {
            Marca::create([
                'nombre_marca' => $this->nombre_marca,
            ]);

            $this->cerrarModal();
            $this->dispatch('marca-guardada');
        } catch (\Throwable $e) {
            Log::error('Error al crear marca: ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'No se pudo crear la marca. Inténtalo de nuevo.');
        }
    }

    public function cerrarModal(){
        $this->resetValidation();
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

    private function reglas(?int $id = null): array
    {
        $rule = Rule::unique('marca', 'nombre_marca')
                    ->where('user_id', Auth::id());

        if ($id !== null) {
            $rule->ignore($id);
        }

        return [
            'nombre_marca' => ['required', 'string', 'min:2', 'max:255', $rule],
        ];
    }
}
