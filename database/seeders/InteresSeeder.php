<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('interes')->insert([
            [
                'interes_general' => 1.10, // 10%
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'interes_general' => 1.20, // 20%
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'interes_general' => 1.30, // 30%
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'interes_general' => 1.40, // 40%
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'interes_general' => 1.50, // 50%
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
