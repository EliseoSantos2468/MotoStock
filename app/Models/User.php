<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_hash',
        'rol_id',
        'creado_por',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Obtiene el rol asignado al usuario.
     * Rol permitido: cualquier usuario autenticado que necesite consultar permisos.
     * Valida: la relación rol_id debe apuntar a un rol existente cuando el usuario tenga acceso.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    /**
     * Obtiene el usuario administrador que creó esta cuenta.
     * Rol permitido: consultas internas del sistema.
     * Valida: solo aplica para usuarios ventas creados por admin_motos.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(self::class, 'creado_por');
    }

    /**
     * Obtiene los usuarios ventas creados por este administrador.
     * Rol permitido: admin_motos.
     * Valida: retorna solo usuarios relacionados por creado_por.
     */
    public function usuariosCreados(): HasMany
    {
        return $this->hasMany(self::class, 'creado_por');
    }
}
