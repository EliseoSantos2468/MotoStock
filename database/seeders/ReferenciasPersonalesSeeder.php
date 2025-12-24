<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenciasPersonalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('referencias_personales')->insert([
            [
                'telefono_ref' => '8888-1234',
                'nombre_ref' => 'Carlos Méndez',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7777-5678',
                'nombre_ref' => 'María López',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7222-3344',
                'nombre_ref' => 'José Ramírez',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7333-4455',
                'nombre_ref' => 'Ana Martínez',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7444-5566',
                'nombre_ref' => 'Luis Torres',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7555-6677',
                'nombre_ref' => 'Patricia Aguilar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7666-7788',
                'nombre_ref' => 'Ricardo Cruz',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7777-8899',
                'nombre_ref' => 'Verónica Salazar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
