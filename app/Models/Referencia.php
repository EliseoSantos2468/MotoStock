<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Referencia extends Model
{
    use BelongsToUser;

    protected $table='referencias_personales';

    protected $fillable=[
        'telefono_ref',
        'nombre_ref',
        'user_id'
    ];

    public function clientes(){
        return $this->belongsToMany(Cliente::class, 'cliente_referencia')->withTimestamps();
    }
}
