<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table='recibos';

    protected $fillable=[
        'fecha',
        'total',
        'id_cliente'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function productos(){
        return $this->belongsToMany(Producto::class, 'producto_recibo')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
