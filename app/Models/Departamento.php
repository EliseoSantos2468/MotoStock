<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';

    protected $fillable = [
        'nombre_departamento'
    ];

    public function clientes(){
        return $this->hasMany(cliente::class);
    }

    public function municipios(){
        return $this->hasMany(Municipio::class);
    }
}
