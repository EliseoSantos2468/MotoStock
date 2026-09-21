<?php

namespace App\Http\Controllers\Api;

use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LibroController extends BaseApiController
{
    /**
     * Lista libros con filtros de búsqueda.
     * Rol permitido: admin_libreria.
     * Valida: filtros opcionales y exclusión de libros inhabilitados.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Libro::query()->latest('id');

        if ($request->filled('search')) {
            $search = mb_strtolower(trim((string) $request->input('search')));

            $query->where(function ($builder) use ($search): void {
                $builder->whereRaw('LOWER(titulo) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(autor) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(isbn) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(categoria) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(editorial) LIKE ?', ['%'.$search.'%']);
            });
        }

        foreach (['titulo', 'autor', 'categoria', 'editorial', 'isbn'] as $campo) {
            if ($request->filled($campo)) {
                $query->whereRaw('LOWER('.$campo.') LIKE ?', ['%'.mb_strtolower(trim((string) $request->input($campo))).'%']);
            }
        }

        if ($request->filled('anio_publicacion')) {
            $query->where('anio_publicacion', (int) $request->input('anio_publicacion'));
        }

        $libros = $query->get();

        return $this->ok('Listado de libros obtenido correctamente', [
            'items' => $libros,
        ]);
    }

    /**
     * Muestra el detalle de un libro y su stock disponible.
     * Rol permitido: admin_libreria.
     * Valida: que el libro exista y no esté eliminado.
     */
    public function show(int $id): JsonResponse
    {
        $libro = Libro::find($id);

        if (! $libro) {
            return $this->notFound('Libro no encontrado');
        }

        return $this->ok('Detalle de libro obtenido correctamente', [
            'libro' => $libro,
            'stock_disponible' => $libro->stock_disponible,
        ]);
    }

    /**
     * Crea un libro nuevo.
     * Rol permitido: admin_libreria.
     * Valida: campos obligatorios, rangos de stock y unicidad del ISBN.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => ['required', 'string', 'max:255'],
            'autor' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:120', 'unique:libro,isbn'],
            'categoria' => ['required', 'string', 'max:255'],
            'editorial' => ['required', 'string', 'max:255'],
            'anio_publicacion' => ['required', 'integer', 'min:1400', 'max:'.(int) now()->year],
            'stock_total' => ['required', 'integer', 'min:0'],
            'stock_disponible' => ['required', 'integer', 'min:0'],
            'precio' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        if ($request->integer('stock_disponible') > $request->integer('stock_total')) {
            return $this->conflict('El stock disponible no puede ser mayor que el stock total');
        }

        $libro = Libro::create([
            'titulo' => $request->input('titulo'),
            'autor' => $request->input('autor'),
            'isbn' => $request->input('isbn'),
            'categoria' => $request->input('categoria'),
            'editorial' => $request->input('editorial'),
            'anio_publicacion' => $request->integer('anio_publicacion'),
            'stock_total' => $request->integer('stock_total'),
            'stock_disponible' => $request->integer('stock_disponible'),
            'precio' => $request->input('precio'),
        ]);

        return $this->ok('Libro creado correctamente', [
            'libro' => $libro,
        ], 201);
    }

    /**
     * Edita un libro.
     * Rol permitido: admin_libreria.
     * Valida: existencia del libro, formato de campos y coherencia del stock.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $libro = Libro::find($id);

        if (! $libro) {
            return $this->notFound('Libro no encontrado');
        }

        $validator = Validator::make($request->all(), [
            'titulo' => ['nullable', 'string', 'max:255'],
            'autor' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:120', Rule::unique('libro', 'isbn')->ignore($libro->id)],
            'categoria' => ['nullable', 'string', 'max:255'],
            'editorial' => ['nullable', 'string', 'max:255'],
            'anio_publicacion' => ['nullable', 'integer', 'min:1400', 'max:'.(int) now()->year],
            'stock_total' => ['nullable', 'integer', 'min:0'],
            'stock_disponible' => ['nullable', 'integer', 'min:0'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        DB::transaction(function () use ($request, $libro): void {
            $campos = ['titulo', 'autor', 'isbn', 'categoria', 'editorial', 'anio_publicacion', 'precio'];

            foreach ($campos as $campo) {
                if ($request->has($campo)) {
                    $libro->{$campo} = $request->input($campo);
                }
            }

            if ($request->has('stock_total')) {
                $libro->stock_total = $request->integer('stock_total');
            }

            if ($request->has('stock_disponible')) {
                $libro->stock_disponible = $request->integer('stock_disponible');
            }

            if ($libro->stock_disponible > $libro->stock_total) {
                $libro->stock_disponible = $libro->stock_total;
            }

            $libro->save();
        });

        return $this->ok('Libro actualizado correctamente', [
            'libro' => $libro->fresh(),
        ]);
    }

    /**
     * Elimina lógicamente un libro.
     * Rol permitido: admin_libreria.
     * Valida: que el libro exista antes de inhabilitarlo.
     */
    public function destroy(int $id): JsonResponse
    {
        $libro = Libro::find($id);

        if (! $libro) {
            return $this->notFound('Libro no encontrado');
        }

        $libro->delete();

        return $this->ok('Libro inhabilitado correctamente');
    }

    /**
     * Resume el stock total y disponible de los libros.
     * Rol permitido: admin_libreria.
     * Valida: devuelve únicamente libros activos.
     */
    public function stockReport(): JsonResponse
    {
        $reporte = Libro::query()
            ->selectRaw('COUNT(*) as total_libros')
            ->selectRaw('SUM(stock_total) as stock_total')
            ->selectRaw('SUM(stock_disponible) as stock_disponible')
            ->first();

        return $this->ok('Reporte de stock obtenido correctamente', [
            'resumen' => $reporte,
        ]);
    }

    /**
     * Registra un préstamo y descuenta una unidad del stock disponible.
     * Rol permitido: admin_libreria.
     * Valida: existencia del libro, stock disponible y fecha de devolución esperada.
     */
    public function prestamo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'libro_id' => ['required', 'integer', 'exists:libro,id'],
            'usuario_solicitante' => ['required', 'string', 'max:500'],
            'fecha_devolucion_esperada' => ['required', 'date', 'after_or_equal:today'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $resultado = DB::transaction(function () use ($request): array {
            $libro = Libro::whereKey($request->integer('libro_id'))->lockForUpdate()->first();

            if (! $libro) {
                return ['type' => 'not_found'];
            }

            if ($libro->stock_disponible <= 0) {
                return ['type' => 'conflict'];
            }

            $libro->stock_disponible -= 1;
            $libro->save();

            $prestamo = Prestamo::create([
                'libro_id' => $libro->id,
                'usuario_solicitante' => $request->input('usuario_solicitante'),
                'fecha_prestamo' => now()->toDateString(),
                'fecha_devolucion_esperada' => $request->date('fecha_devolucion_esperada')->toDateString(),
                'fecha_devolucion_real' => null,
                'estado' => Prestamo::ESTADO_ACTIVO,
                'admin_id' => $request->user()->id,
            ]);

            return ['type' => 'ok', 'prestamo' => $prestamo->load('libro')];
        });

        return match ($resultado['type']) {
            'not_found' => $this->notFound('Libro no encontrado'),
            'conflict' => $this->conflict('No se puede registrar el préstamo porque no hay stock disponible'),
            default => $this->ok('Préstamo registrado correctamente', [
                'prestamo' => $resultado['prestamo'],
            ], 201),
        };
    }

    /**
     * Registra la devolución de un préstamo activo.
     * Rol permitido: admin_libreria.
     * Valida: que el préstamo exista, esté activo y que el libro recupere su stock.
     */
    public function devolucion(int $id): JsonResponse
    {
        $resultado = DB::transaction(function () use ($id): array {
            $prestamo = Prestamo::whereKey($id)->lockForUpdate()->first();

            if (! $prestamo) {
                return ['type' => 'not_found'];
            }

            if ($prestamo->estado !== Prestamo::ESTADO_ACTIVO) {
                return ['type' => 'conflict'];
            }

            $libro = Libro::withTrashed()->whereKey($prestamo->libro_id)->lockForUpdate()->first();

            if (! $libro) {
                return ['type' => 'not_found_libro'];
            }

            $libro->stock_disponible += 1;
            $libro->save();

            $prestamo->fecha_devolucion_real = now()->toDateString();
            $prestamo->estado = Prestamo::ESTADO_DEVUELTO;
            $prestamo->save();

            return ['type' => 'ok', 'prestamo' => $prestamo->load('libro')];
        });

        return match ($resultado['type']) {
            'not_found' => $this->notFound('Préstamo no encontrado'),
            'not_found_libro' => $this->notFound('Libro asociado al préstamo no encontrado'),
            'conflict' => $this->conflict('Solo se pueden devolver préstamos en estado activo'),
            default => $this->ok('Devolución registrada correctamente', [
                'prestamo' => $resultado['prestamo'],
            ]),
        };
    }
}