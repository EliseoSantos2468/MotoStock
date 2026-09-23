<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user) return;

        DB::table('marca')->insert([
            ['nombre_marca' => 'Yamaha', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Suzuki', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Honda', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Kawasaki', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Harley-Davidson', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Ducati', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'KTM', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'BMW Motorrad', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Triumph', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_marca' => 'Royal Enfield', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

