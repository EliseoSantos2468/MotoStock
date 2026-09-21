<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'libro';

    protected $fillable = [
        'titulo',
        'autor',
        'isbn',
        'categoria',
        'editorial',
        'anio_publicacion',
        'stock_total',
        'stock_disponible',
        'precio',
    ];

    protected function casts(): array
    {
        return [
            'anio_publicacion' => 'integer',
            'stock_total' => 'integer',
            'stock_disponible' => 'integer',
            'precio' => 'decimal:2',
        ];
    }

    /**
     * Obtiene los préstamos del libro.
     * Rol permitido: administración de librería.
     * Valida: la relación siempre apunta al libro actual.
     */
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'libro_id');
    }
}