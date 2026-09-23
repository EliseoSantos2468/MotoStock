<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $rolAdminMotos = Role::where('nombre', Role::ADMIN_MOTOS)->first();

        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'password_hash' => Hash::make('password123'),
            'rol_id' => $rolAdminMotos?->id,
            'activo' => true,
        ]);

        // seeders
        $this->call([
            DepartamentoMunicipioSeeder::class,
            ClasificacionSeeder::class,
            InteresSeeder::class,
            ReferenciasPersonalesSeeder::class,
            MarcaSeeder::class,
            ProductoSeeder::class,
            ProductoMarcaSeeder::class,
            ClienteSeeder::class,
            ClienteReferenciaSeeder::class,
        ]);
    }
}
