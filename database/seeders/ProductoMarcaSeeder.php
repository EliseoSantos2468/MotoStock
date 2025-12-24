<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoMarcaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('producto_marca')->insert([
            [
                'producto_id' => 1,
                'marca_id' => 1,
                'cantidad' => 10,
                'precio_cliente' => 650.00,
                'precio_mayoreo' => 600.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 2,
                'marca_id' => 2,
                'cantidad' => 15,
                'precio_cliente' => 55.00,
                'precio_mayoreo' => 50.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 3,
                'marca_id' => 3, // Honda
                'cantidad' => 20,
                'precio_cliente' => 30.00,
                'precio_mayoreo' => 25.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 4,
                'marca_id' => 4, // Kawasaki
                'cantidad' => 18,
                'precio_cliente' => 40.00,
                'precio_mayoreo' => 35.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 5,
                'marca_id' => 5, // Harley-Davidson
                'cantidad' => 12,
                'precio_cliente' => 75.00,
                'precio_mayoreo' => 70.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 6,
                'marca_id' => 6, // Ducati
                'cantidad' => 25,
                'precio_cliente' => 45.00,
                'precio_mayoreo' => 40.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 7,
                'marca_id' => 7, // KTM
                'cantidad' => 30,
                'precio_cliente' => 90.00,
                'precio_mayoreo' => 85.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 8,
                'marca_id' => 8, // BMW Motorrad
                'cantidad' => 14,
                'precio_cliente' => 60.00,
                'precio_mayoreo' => 55.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 9,
                'marca_id' => 9, // Triumph
                'cantidad' => 10,
                'precio_cliente' => 120.00,
                'precio_mayoreo' => 110.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'producto_id' => 10,
                'marca_id' => 10, // Royal Enfield
                'cantidad' => 22,
                'precio_cliente' => 35.00,
                'precio_mayoreo' => 30.00,
                'venta_producto' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
