<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    protected $table = 'cliente';

    protected $fillable = [
        'nombres_cliente',
        'apellidos_cliente',
        'dui_cliente',
        'telefono_cliente',
        'nit_cliente',
        'email_cliente',
        'monto_max',
        'barrio',
        'id_clasificacion',
        'id_departamento',
        'id_municipio',
    ];
    
    public function clasificacion(){
        return $this->belongsTo(clasificacion::class, 'id_clasificacion');
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function recibos(){
        return $this->hasMany(Recibo::class,'id_cliente');
    }

    public function referencias(){
        return $this->belongsToMany(referencia::class, 'cliente_referencia')->withTimestamps();
    }

    public function productos(){
        return $this->belongsToMany(Producto::class, 'cliente_producto')
                                    ->withPivot('cantidad')
                                    ->withTimestamps();
    }

    public function credito(){
        return $this->hasMany(Credito::class);
    }

    // Método para actualizar un cliente
    public function actualizar(Request $request, $id)
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'nombres_cliente' => 'required|string|max:255',
            'apellidos_cliente' => 'required|string|max:255',
            'dui_cliente' => 'required|string|max:10',
            'telefono_cliente' => 'required|string|max:15',
            'nit_cliente' => 'required|string|max:20',
            'email_cliente' => 'required|email|max:255',
            'monto_max' => 'required|numeric',
            'barrio' => 'nullable|string|max:255',
            'id_clasificacion' => 'required|exists:clasificaciones,id', // Ejemplo de validación
            'id_departamento' => 'required|exists:departamentos,id',
            'id_municipio' => 'required|exists:municipios,id',
        ]);

        // Encontrar el cliente por ID
        $cliente = Cliente::findOrFail($id);

        // Actualizar los datos del cliente
        $cliente->update($validatedData);

        // Redirigir a una vista o mostrar un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }
}
