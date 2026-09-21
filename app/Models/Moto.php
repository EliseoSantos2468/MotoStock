<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moto extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const ESTADO_DISPONIBLE = 'disponible';
    public const ESTADO_VENDIDA = 'vendida';
    public const ESTADO_RESERVADA = 'reservada';

    public const ESTADOS = [
        self::ESTADO_DISPONIBLE,
        self::ESTADO_VENDIDA,
        self::ESTADO_RESERVADA,
    ];

    protected $table = 'moto';

    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'color',
        'precio',
        'stock',
        'num_chasis',
        'num_motor',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'precio' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Obtiene los movimientos de stock de la moto.
     * Rol permitido: administración y auditoría interna.
     * Valida: la relación queda acotada a la moto actual.
     */
    public function stockMovimientos(): HasMany
    {
        return $this->hasMany(StockMovimiento::class, 'moto_id');
    }
}