<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fechas extends Model
{
    protected $table='fechas';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite',
    ];

    public function credito(){
        return $this->hasMany(Credito::class);
    }
}
