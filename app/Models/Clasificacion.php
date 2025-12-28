<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clasificacion extends Model
{
    // use HasFactory;

    protected $table = 'clasificacion';

    protected $fillable= [
        'nombre_clasificacion',
    ];

    public function clientes(){
        return $this->hasMany(cliente::class, 'id_clasificacion');
    }
}
