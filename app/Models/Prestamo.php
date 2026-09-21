<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    use HasFactory;

    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_DEVUELTO = 'devuelto';

    protected $table = 'prestamo';

    protected $fillable = [
        'libro_id',
        'usuario_solicitante',
        'fecha_prestamo',
        'fecha_devolucion_esperada',
        'fecha_devolucion_real',
        'estado',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'date',
            'fecha_devolucion_esperada' => 'date',
            'fecha_devolucion_real' => 'date',
        ];
    }

    /**
     * Obtiene el libro prestado.
     * Rol permitido: administración de librería.
     * Valida: cada préstamo debe apuntar a un libro existente.
     */
    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    /**
     * Obtiene el administrador que registró el préstamo.
     * Rol permitido: administración de librería.
     * Valida: el admin_id debe corresponder a un usuario autenticado.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}