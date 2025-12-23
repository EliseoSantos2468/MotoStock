<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';

    protected $fillable = [
        'nombre_marca',
    ];

    public function productos(){
        return $this->belongsToMany(Producto::class, 'producto_marca')
                    ->withPivot('cantidad')
                    ->withPivot('precio_cliente')
                    ->withPivot('precio_mayoreo')
                    ->withPivot('venta_producto')
                    ->withTimestamps();
    }
}
