<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123'
        ]);

        // seeders
        $this->call([
            ReferenciasPersonalesSeeder::class,
            ClasificacionSeeder::class,
            InteresSeeder::class,
            DepartamentoMunicipioSeeder::class,
            MarcaSeeder::class,
            ProductoSeeder::class,
            ProductoMarcaSeeder::class,
            ClienteSeeder::class,
            ClienteReferenciaSeeder::class,
        ]);
    }
}
