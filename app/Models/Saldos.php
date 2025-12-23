<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldos extends Model
{
    protected $table = 'saldos';

    protected $fillable = [
        'saldo_mora',
        'saldo_p_interes',
        'saldo_pendiente',
        'credito_id'
    ];

    public function creditos(){
        return $this->belongsTo(Credito::class,'credito_id');
    }
}
