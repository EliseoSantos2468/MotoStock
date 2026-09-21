<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Obtener solo registros del usuario autenticado
     */
    public static function bootBelongsToUser()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });

        // Asignar user_id automáticamente al crear
        static::creating(function ($model) {
            if (Auth::check() && is_null($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope para obtener registros de un usuario específico
     */
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para obtener sin filtro de usuario
     */
    public function scopeWithoutUserFilter(Builder $query)
    {
        return $query->withoutGlobalScope('user_id');
    }
}
