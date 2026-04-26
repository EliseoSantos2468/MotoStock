<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clasificacion extends Model
{
    use BelongsToUser;
    // use HasFactory;

    protected $table = 'clasificacion';

    protected $fillable= [
        'nombre_clasificacion',
        'user_id',
    ];

    public function clientes(){
        return $this->hasMany(cliente::class, 'id_clasificacion');
    }
}
