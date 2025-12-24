<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar productos
        DB::table('producto')->insert([
            ['nombre_producto' => 'Llantas Traseras', 'descripcion_producto' => 'Llantas para vehículos', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Llantas Delanteras', 'descripcion_producto' => 'Llantas para vehículos', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Aceite de Motor', 'descripcion_producto' => 'Lubricante para motores de motocicleta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Filtro de Aire', 'descripcion_producto' => 'Filtro para entrada de aire del motor', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Batería 12V', 'descripcion_producto' => 'Batería estándar para motocicleta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Pastillas de Freno', 'descripcion_producto' => 'Juego de pastillas para freno de disco', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Cadena de Transmisión', 'descripcion_producto' => 'Cadena para transmisión de motocicleta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Espejos Laterales', 'descripcion_producto' => 'Juego de espejos para motocicleta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Manillar Deportivo', 'descripcion_producto' => 'Manillar para motocicleta estilo sport', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_producto' => 'Luces LED', 'descripcion_producto' => 'Kit de luces LED para motocicleta', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
