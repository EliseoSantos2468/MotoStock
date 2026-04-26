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

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123'
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
