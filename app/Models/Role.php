<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN_MOTOS = 'admin_motos';
    public const ADMIN_LIBRERIA = 'admin_libreria';
    public const VENTAS = 'ventas';

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Obtiene los usuarios asociados al rol.
     * Rol permitido: consultas administrativas.
     * Valida: solo devuelve usuarios con este rol_id.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}