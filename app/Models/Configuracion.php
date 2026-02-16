<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    
    protected $fillable = [
        'color_primario', 
        'color_secundario', 
        'correo_empresa'
    ];
}