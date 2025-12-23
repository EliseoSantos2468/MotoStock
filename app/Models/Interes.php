<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Interes extends Model
{
    protected $table = 'interes'; // Nombre de la tabla

    protected $fillable = ['interes_general']; // Campos que se pueden llenar

    public function credito(){
        return $this->hasMany(Credito::class);
    }
}
