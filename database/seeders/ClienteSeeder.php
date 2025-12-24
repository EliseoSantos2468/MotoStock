<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cliente')->insert([
            [
                'nombres_cliente' => 'Eliseo Antonio',
                'apellidos_cliente' => 'Santos Diaz',
                'dui_cliente' => '06448993-1',
                'telefono_cliente' => '7879-0673',
                'nit_cliente' => '002989398493',
                'email_cliente'=> 'Eliseo@gmail.com',
                'barrio'=> 'Cuscatlán',
                'monto_max' => 1000.00,
                'id_clasificacion' => 3,
                'id_departamento' => 1,
                'id_municipio' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres_cliente' => 'Edras',
                'apellidos_cliente' => 'Lazo',
                'dui_cliente' => '06448993-2',
                'telefono_cliente' => '7879-0673',
                'nit_cliente' => '002989398493',
                'email_cliente'=> 'Edras@gmail.com',
                'barrio'=> 'Cuscatlán',
                'monto_max' => 1000.00,
                'id_clasificacion' => 3,
                'id_departamento' => 1,
                'id_municipio' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres_cliente' => 'María José',
                'apellidos_cliente' => 'Gómez Álvarez',
                'dui_cliente' => '05412345-7',
                'telefono_cliente' => '7200-1122',
                'nit_cliente' => '031234567890',
                'email_cliente'=> 'maria.gomez@example.com',
                'barrio'=> 'San Benito',
                'monto_max' => 850.00,
                'id_clasificacion' => 2,
                'id_departamento' => 2,
                'id_municipio' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres_cliente' => 'Carlos Andrés',
                'apellidos_cliente' => 'Mejía Pérez',
                'dui_cliente' => '06123456-3',
                'telefono_cliente' => '7312-4455',
                'nit_cliente' => '042345678912',
                'email_cliente'=> 'carlos.mejia@example.com',
                'barrio'=> 'Escalón',
                'monto_max' => 1200.00,
                'id_clasificacion' => 1,
                'id_departamento' => 3,
                'id_municipio' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres_cliente' => 'Ana Fernanda',
                'apellidos_cliente' => 'Rivas López',
                'dui_cliente' => '07234567-8',
                'telefono_cliente' => '7688-9900',
                'nit_cliente' => '053456789012',
                'email_cliente'=> 'ana.rivas@example.com',
                'barrio'=> 'Santa Anita',
                'monto_max' => 950.00,
                'id_clasificacion' => 2,
                'id_departamento' => 4,
                'id_municipio' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
