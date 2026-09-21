<?php

namespace App\Http\Controllers\Api;

use App\Models\Moto;
use App\Models\StockMovimiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MotoController extends BaseApiController
{
    /**
     * Lista motos con filtros de búsqueda.
     * Rol permitido: admin_motos y ventas.
     * Valida: filtros opcionales y exclusión de registros eliminados.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Moto::query()->latest('id');

        if ($request->filled('search')) {
            $search = mb_strtolower(trim((string) $request->input('search')));

            $query->where(function ($builder) use ($search): void {
                $builder->whereRaw('LOWER(marca) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(modelo) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(num_chasis) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(num_motor) LIKE ?', ['%'.$search.'%']);
            });
        }

        foreach (['marca', 'modelo', 'color', 'estado'] as $campo) {
            if ($request->filled($campo)) {
                $query->whereRaw('LOWER('.$campo.') LIKE ?', ['%'.mb_strtolower(trim((string) $request->input($campo))).'%']);
            }
        }

        if ($request->filled('anio')) {
            $query->where('anio', (int) $request->input('anio'));
        }

        $motos = $query->get();

        return $this->ok('Listado de motos obtenido correctamente', [
            'items' => $motos,
        ]);
    }

    /**
     * Muestra el detalle de una moto y su stock actual.
     * Rol permitido: admin_motos y ventas.
     * Valida: que el id exista y no esté eliminado.
     */
    public function show(int $id): JsonResponse
    {
        $moto = Moto::find($id);

        if (! $moto) {
            return $this->notFound('Moto no encontrada');
        }

        return $this->ok('Detalle de moto obtenido correctamente', [
            'moto' => $moto,
            'stock_actual' => $moto->stock,
        ]);
    }

    /**
     * Crea una nueva moto.
     * Rol permitido: admin_motos.
     * Valida: datos obligatorios, estado permitido y unicidad de chasis y motor.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'anio' => ['required', 'integer', 'min:1900', 'max:'.(int) now()->addYear()->year],
            'color' => ['required', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'num_chasis' => ['required', 'string', 'max:120', 'unique:moto,num_chasis'],
            'num_motor' => ['required', 'string', 'max:120', 'unique:moto,num_motor'],
            'estado' => ['nullable', 'string', Rule::in(Moto::ESTADOS)],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $moto = Moto::create([
            'marca' => $request->input('marca'),
            'modelo' => $request->input('modelo'),
            'anio' => $request->integer('anio'),
            'color' => $request->input('color'),
            'precio' => $request->input('precio'),
            'stock' => $request->integer('stock'),
            'num_chasis' => $request->input('num_chasis'),
            'num_motor' => $request->input('num_motor'),
            'estado' => $request->input('estado', Moto::ESTADO_DISPONIBLE),
        ]);

        return $this->ok('Moto creada correctamente', [
            'moto' => $moto,
        ], 201);
    }

    /**
     * Edita una moto o su stock y registra el movimiento correspondiente.
     * Rol permitido: admin_motos.
     * Valida: existencia de la moto, reglas de formato y consistencia del nuevo stock.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $moto = Moto::find($id);

        if (! $moto) {
            return $this->notFound('Moto no encontrada');
        }

        $validator = Validator::make($request->all(), [
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:'.(int) now()->addYear()->year],
            'color' => ['nullable', 'string', 'max:100'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'num_chasis' => ['nullable', 'string', 'max:120', Rule::unique('moto', 'num_chasis')->ignore($moto->id)],
            'num_motor' => ['nullable', 'string', 'max:120', Rule::unique('moto', 'num_motor')->ignore($moto->id)],
            'estado' => ['nullable', 'string', Rule::in(Moto::ESTADOS)],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        DB::transaction(function () use ($request, $moto): void {
            $datosActualizados = $request->only([
                'marca',
                'modelo',
                'anio',
                'color',
                'precio',
                'num_chasis',
                'num_motor',
                'estado',
            ]);

            foreach ($datosActualizados as $campo => $valor) {
                if ($valor !== null) {
                    $moto->{$campo} = $valor;
                }
            }

            if ($request->has('stock')) {
                $nuevoStock = $request->integer('stock');

                if ($nuevoStock !== $moto->stock) {
                    $diferencia = $nuevoStock - $moto->stock;
                    $moto->stock = $nuevoStock;

                    StockMovimiento::create([
                        'moto_id' => $moto->id,
                        'tipo' => $diferencia >= 0 ? StockMovimiento::TIPO_INGRESO : StockMovimiento::TIPO_AJUSTE,
                        'cantidad' => abs($diferencia),
                        'usuario_id' => $request->user()->id,
                        'fecha' => now(),
                    ]);
                }
            }

            $moto->save();
        });

        return $this->ok('Moto actualizada correctamente', [
            'moto' => $moto->fresh(),
        ]);
    }

    /**
     * Elimina lógicamente una moto.
     * Rol permitido: admin_motos.
     * Valida: que la moto exista antes de inhabilitarla.
     */
    public function destroy(int $id): JsonResponse
    {
        $moto = Moto::find($id);

        if (! $moto) {
            return $this->notFound('Moto no encontrada');
        }

        $moto->delete();

        return $this->ok('Moto inhabilitada correctamente');
    }

    /**
     * Resume el stock por marca.
     * Rol permitido: admin_motos y ventas.
     * Valida: consolida únicamente motos activas.
     */
    public function stockReport(): JsonResponse
    {
        $reporte = Moto::query()
            ->select('marca')
            ->selectRaw('COUNT(*) as total_modelos')
            ->selectRaw('SUM(stock) as stock_total')
            ->selectRaw("SUM(CASE WHEN estado = 'disponible' THEN stock ELSE 0 END) as stock_disponible")
            ->groupBy('marca')
            ->orderBy('marca')
            ->get();

        return $this->ok('Reporte de stock obtenido correctamente', [
            'items' => $reporte,
        ]);
    }

    /**
     * Registra una venta y descuenta una unidad del stock.
     * Rol permitido: admin_motos y ventas.
     * Valida: existencia de la moto, stock mayor a cero y estado disponible.
     */
    public function venta(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'moto_id' => ['required', 'integer', 'exists:moto,id'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $resultado = DB::transaction(function () use ($request): array {
            $moto = Moto::whereKey($request->integer('moto_id'))->lockForUpdate()->first();

            if (! $moto) {
                return ['type' => 'not_found'];
            }

            if ($moto->stock <= 0 || $moto->estado !== Moto::ESTADO_DISPONIBLE) {
                return ['type' => 'conflict'];
            }

            $moto->stock -= 1;

            if ($moto->stock === 0) {
                $moto->estado = Moto::ESTADO_VENDIDA;
            }

            $moto->save();

            StockMovimiento::create([
                'moto_id' => $moto->id,
                'tipo' => StockMovimiento::TIPO_VENTA,
                'cantidad' => 1,
                'usuario_id' => $request->user()->id,
                'fecha' => now(),
            ]);

            return ['type' => 'ok', 'moto' => $moto];
        });

        return match ($resultado['type']) {
            'not_found' => $this->notFound('Moto no encontrada'),
            'conflict' => $this->conflict('No se puede registrar la venta porque el stock es insuficiente o la moto no está disponible'),
            default => $this->ok('Venta registrada correctamente', [
                'moto' => $resultado['moto'],
            ], 201),
        };
    }
}