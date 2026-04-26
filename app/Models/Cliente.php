<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;


class Cliente extends Model
{
    use BelongsToUser;

    protected $table = 'cliente';

    protected $fillable = [
        'nombres_cliente',
        'apellidos_cliente',
        'dui_cliente',
        'telefono_cliente',
        'email_cliente',
        'monto_max',
        'barrio',
        'id_clasificacion',
        'id_departamento',
        'id_municipio',
        'user_id',
    ];
    
    public function clasificacion(){
        return $this->belongsTo(clasificacion::class, 'id_clasificacion');
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function municipio(){
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function recibos(){
        return $this->hasMany(Recibo::class,'id_cliente');
    }

    public function referencias(){
        return $this->belongsToMany(referencia::class, 'cliente_referencia')->withTimestamps();
    }

    public function productos(){
        return $this->belongsToMany(Producto::class, 'cliente_producto')
                                    ->withPivot('cantidad')
                                    ->withTimestamps();
    }

    public function credito(){
        return $this->hasMany(Credito::class);
    }
}
