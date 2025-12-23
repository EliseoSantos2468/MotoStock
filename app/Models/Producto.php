<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table='producto';

    protected $fillable = [
        'nombre_producto',
        'descripcion_producto',
    ];

    public function marcas(){
        return $this->belongsToMany(Marca::class, 'producto_marca')
                    ->withPivot('cantidad')
                    ->withPivot('precio_cliente')
                    ->withPivot('precio_mayoreo')
                    ->withPivot('venta_producto')
                    ->withTimestamps();
    }

    public function recibos(){
        return $this->belongsToMany(Recibo::class, 'producto_recibo')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    public function clientes(){
        return $this->belongsToMany(Cliente::class, 'cliente_producto')
                                    ->withPivot('cantidad')
                                    ->withTimestamps();
    }
}
