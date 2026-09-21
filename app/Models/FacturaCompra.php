<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class FacturaCompra extends Model
{
    use BelongsToUser;

    protected $table = 'facturas_compra';

    protected $fillable = [
        'numero_factura',
        'fecha',
        'estado',
        'proveedor_id',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function detalles()
    {
        return $this->hasMany(FacturaCompraDetalle::class, 'factura_compra_id');
    }
}
