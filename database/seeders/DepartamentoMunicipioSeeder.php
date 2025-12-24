<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Departamento;
use App\Models\Municipio;

class DepartamentoMunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://api.npoint.io/253f0ee259ef1620a547/departamentos/');

       
        if ($response->successful()) {
            $departamentos = $response->json();

            foreach ($departamentos as $dep) {
                // Crear el departamento
                $departamento = Departamento::updateOrCreate(
                    ['nombre_departamento' => $dep['nombre']],
                    ['nombre_departamento' => $dep['nombre']]
                );

                // Crear municipios
                if (isset($dep['municipios']) && is_array($dep['municipios'])) {
                    foreach ($dep['municipios'] as $mun) {
                        Municipio::updateOrCreate(
                            [
                                'nombre_municipio' => $mun['nombre'],
                                'departamento_id' => $departamento->id,
                            ],
                            [
                                'nombre_municipio' => $mun['nombre'],
                                'departamento_id' => $departamento->id,
                            ]
                        );
                    }
                }
            }
        } else {
            $this->command->error('No se pudo obtener los datos de los departamentos.');
        }
    }
}
