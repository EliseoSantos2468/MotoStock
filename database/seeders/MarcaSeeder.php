<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marca')->insert([
            ['nombre_marca' => 'Yamaha'],
            ['nombre_marca' => 'Suzuki'],
            ['nombre_marca' => 'Honda'],
            ['nombre_marca' => 'Kawasaki'],
            ['nombre_marca' => 'Harley-Davidson'],
            ['nombre_marca' => 'Ducati'],
            ['nombre_marca' => 'KTM'],
            ['nombre_marca' => 'BMW Motorrad'],
            ['nombre_marca' => 'Triumph'],
            ['nombre_marca' => 'Royal Enfield'],
        ]);
    }
}

