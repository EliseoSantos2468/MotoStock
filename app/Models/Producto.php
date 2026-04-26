<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Producto extends Model
{
    use BelongsToUser;

    protected $table='producto';

    protected $fillable = [
        'nombre_producto',
        'descripcion_producto',
        'user_id',
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
