<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaldoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('saldos')->insert([
            'saldo_mora' => 500, // Puedes ajustar según tu necesidad
            'saldo_p_interes' => 0.00,
            'saldo_pendiente' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
