<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referencia extends Model
{
    protected $table='referencias_personales';

    protected $fillable=[
        'telefono_ref',
        'nombre_ref'
    ];

    public function clientes(){
        return $this->belongsToMany(cliente::class, 'cliente_referencia')->withTimestamps();
    }
}
