<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Credito extends Model
{
    use BelongsToUser;

    protected $table = 'credito';

    protected $fillable = [
        'monto_facturado',
        'interes_moratorio',
        'prima',
        'cuota_id',
        'interes_id',
        'cliente_id',
        'fechas_id',
        'user_id',
    ];

    public function cuotas(){
        return $this->belongsTo(Cuota::class, 'cuota_id');
    }
    public function intereses(){
        return $this->belongsTo(Interes::class, 'interes_id');
    }
    public function saldo(){
        return $this->hasMany(Saldos::class);
    }
    public function clientes(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function fechas(){
        return $this->belongsTo(Fechas::class, 'fechas_id');
    }
}
