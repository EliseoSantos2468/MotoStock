<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use BelongsToUser;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_proveedor',
        'codigo_pais',
        'telefono',
        'email',
        'user_id',
    ];

    public function facturasCompra()
    {
        return $this->hasMany(FacturaCompra::class, 'proveedor_id');
    }
}
