<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table='municipio';

    protected $fillable = [
        'nombre_municipio',
        'departamento_id'
    ];

    public function departamento(){
        return $this->belongsTo(Departamento::class);
    }
}
