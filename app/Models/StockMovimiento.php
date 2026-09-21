<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovimiento extends Model
{
    use HasFactory;

    public const TIPO_VENTA = 'venta';
    public const TIPO_AJUSTE = 'ajuste';
    public const TIPO_INGRESO = 'ingreso';

    protected $table = 'stock_movimientos';

    protected $fillable = [
        'moto_id',
        'tipo',
        'cantidad',
        'usuario_id',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'fecha' => 'datetime',
        ];
    }

    /**
     * Obtiene la moto asociada al movimiento.
     * Rol permitido: administración y consultas de stock.
     * Valida: cada movimiento pertenece a una sola moto.
     */
    public function moto(): BelongsTo
    {
        return $this->belongsTo(Moto::class, 'moto_id');
    }

    /**
     * Obtiene el usuario que registró el movimiento.
     * Rol permitido: auditoría interna.
     * Valida: el movimiento siempre debe tener un usuario responsable.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}