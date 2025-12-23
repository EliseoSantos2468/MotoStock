<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';
    protected $fillable = [
        'numero_cuotas',
        'valor_cuota',
    ];

    public function credito(){
        return $this->hasMany(Credito::class);
    }
}
