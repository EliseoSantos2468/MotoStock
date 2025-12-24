<?php

namespace Database\Seeders;

use App\Models\Clasificacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClasificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = ['Frecuente', 'Moroso', 'Nuevo'];

        foreach($categorias as $categoria){
            Clasificacion::updateOrCreate(
                ['nombre_clasificacion' => $categoria]
            );
        }
    }
}
