<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaCompraDetalle extends Model
{
    protected $table = 'factura_compra_detalles';

    protected $fillable = [
        'factura_compra_id',
        'producto_id',
        'marca_id',
        'cantidad_esperada',
        'cantidad_recibida',
        'precio_unitario',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    public function facturaCompra()
    {
        return $this->belongsTo(FacturaCompra::class, 'factura_compra_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
}
