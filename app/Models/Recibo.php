<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Recibo extends Model
{
    use BelongsToUser;

    protected $table='recibos';

    protected $fillable=[
        'fecha',
        'total',
        'id_cliente',
        'email_invitado',
        'user_id'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function productos(){
        return $this->belongsToMany(Producto::class, 'producto_recibo')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }
}
