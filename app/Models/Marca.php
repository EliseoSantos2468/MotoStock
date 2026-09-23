<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Marca extends Model
{
    use BelongsToUser;

    protected $table = 'marca';

    protected $fillable = [
        'nombre_marca',
        'user_id',
    ];

    public function productos(){
        return $this->belongsToMany(Producto::class, 'producto_marca')
                    ->withPivot('cantidad')
                    ->withPivot('precio_cliente')
                    ->withPivot('precio_mayoreo')
                    ->withPivot('venta_producto')
                    ->withTimestamps();
    }
}
