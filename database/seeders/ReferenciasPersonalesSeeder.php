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
        $user = \App\Models\User::first();
        if (!$user) return;

        DB::table('referencias_personales')->insert([
            [
                'telefono_ref' => '8888-1234',
                'nombre_ref' => 'Carlos Méndez',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7777-5678',
                'nombre_ref' => 'María López',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7222-3344',
                'nombre_ref' => 'José Ramírez',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7333-4455',
                'nombre_ref' => 'Ana Martínez',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7444-5566',
                'nombre_ref' => 'Luis Torres',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7555-6677',
                'nombre_ref' => 'Patricia Aguilar',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7666-7788',
                'nombre_ref' => 'Ricardo Cruz',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'telefono_ref' => '7777-8899',
                'nombre_ref' => 'Verónica Salazar',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
