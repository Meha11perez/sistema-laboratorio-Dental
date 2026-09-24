<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MovimientoInventario;
use App\Models\InventarioTecnico;
use App\Models\Tecnico;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
 
class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();

        $esTecnico =
            $usuario?->role?->nombre === 'Técnico';


        // =====================================================
        // INVENTARIO DEL TÉCNICO
        // =====================================================
        if ($esTecnico) {

            $tecnico = Tecnico::where(
                'user_id',
                $usuario->id
            )->first();

            if (!$tecnico) {
                abort(
                    403,
                    'El usuario no tiene un técnico asociado.'
                );
            }

            $query = InventarioTecnico::with('material')
                ->where(
                    'tecnico_id',
                    $tecnico->id
                );


            if ($request->filled('buscar')) {

                $buscar = $request->buscar;

                $query->whereHas(
                    'material',
                    function ($q) use ($buscar) {

                        $q->where(
                            'nombre',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhere(
                            'codigo',
                            'like',
                            "%{$buscar}%"
                        );
                    }
                );
            }

            $inventarioTecnico = $query
                ->orderByDesc('cantidad')
                ->paginate(10)
                ->withQueryString();


            return view(
                'inventario.tecnico',
                compact(
                    'inventarioTecnico',
                    'tecnico'
                )
            );
        }

        // INVENTARIO GENERAL
       
        $query = Material::query();

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    "%{$buscar}%"
                )
                ->orWhere(
                    'codigo',
                    'like',
                    "%{$buscar}%"
                );
            });
        }

        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {
                $query->where('estado', true);
            }

            if ($request->estado === 'inactivo') {
                $query->where('estado', false);
            }

            if ($request->estado === 'bajo') {
                $query->whereColumn(
                    'stock_actual',
                    '<=',
                    'stock_minimo'
                );
            }
        }

        $materiales = $query
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $totalMateriales =
            Material::count();

        $materialesActivos =
            Material::where(
                'estado',
                true
            )->count();


        $stockBajo =
            Material::whereColumn(
                'stock_actual',
                '<=',
                'stock_minimo'
            )->count();


        return view(
            'inventario.index',
            compact(
                'materiales',
                'totalMateriales',
                'materialesActivos',
                'stockBajo'
            )
        );
    }
    public function create()
    {
        return view('inventario.create');
    }
    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50|unique:materiales,codigo',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'costo_unitario' => 'required|numeric|min:0',
        ]);

        $datos['estado'] = true;

        Material::create($datos);

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Material registrado correctamente.');
    }
   public function show(Material $material)
    {
        $material->load([
            'movimientosInventario.usuarioRegistro'
        ]);

        return view('inventario.show', compact('material'));
    }
    public function createAsignacion(Material $material)
    {
        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->orderBy('id')
            ->get();

        return view(
            'inventario.asignar',
            compact(
                'material',
                'tecnicos'
            )
        );
    }

    public function storeAsignacion(
    Request $request,
    Material $material
) {
    $datos = $request->validate([
        'tecnico_id' => [
            'required',
            'exists:tecnicos,id',
        ],

        'cantidad' => [
            'required',
            'numeric',
            'min:0.01',
        ],

        'observaciones' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ]);


    DB::transaction(function () use (
        $datos,
        $material
        ) {

        // ==========================================
        // BLOQUEAR MATERIAL GENERAL
        // ==========================================
        $materialActual = Material::where(
            'id',
            $material->id
        )
        ->lockForUpdate()
        ->firstOrFail();


        $cantidad = (float) $datos['cantidad'];

        $stockAnterior =
            (float) $materialActual->stock_actual;


        // ==========================================
        // VALIDAR STOCK
        // ==========================================
        if ($cantidad > $stockAnterior) {

            throw
             \Illuminate\Validation\ValidationException
                ::withMessages([
                    'cantidad' =>
                        'No hay suficiente stock general. Disponible: '
                        . number_format($stockAnterior, 2),
                ]);
        }


        // ==========================================
        // RESTAR INVENTARIO GENERAL
        // ==========================================
        $stockNuevo =
            $stockAnterior - $cantidad;


        $materialActual->update([
            'stock_actual' => $stockNuevo,
        ]);


        // ==========================================
        // INVENTARIO DEL TÉCNICO
        // ==========================================
        $inventarioTecnico =
            InventarioTecnico::firstOrCreate(
                [
                    'tecnico_id' =>
                        $datos['tecnico_id'],
                         'material_id' =>
                        $materialActual->id,
                ],
                [
                    'cantidad' => 0,
                ]
            );


        $stockTecnicoAnterior =
            (float) $inventarioTecnico->cantidad;


        $stockTecnicoNuevo =
            $stockTecnicoAnterior + $cantidad;


        $inventarioTecnico->update([
            'cantidad' =>
                $stockTecnicoNuevo,
        ]);
        // ==========================================
        // HISTORIAL DEL MOVIMIENTO
        // ==========================================
        MovimientoInventario::create([
            'material_id' =>
                $materialActual->id,

            'tecnico_id' =>
                $datos['tecnico_id'],

            'orden_trabajo_id' =>
                null,

            'registrado_por' =>
                auth()->id(),

            'tipo_movimiento' =>
                'Salida',

            'cantidad' =>
                $cantidad,

            'stock_anterior' =>
                $stockAnterior,
            'stock_nuevo' =>
                $stockNuevo,

            'fecha_movimiento' =>
                now(),

            'observaciones' =>
                $datos['observaciones']
                ??
                (
                    'Material asignado a técnico. '
                    . 'Inventario técnico: '
                    . number_format(
                        $stockTecnicoAnterior,
                        2
                    )
                    . ' → '
                    . number_format(
                        $stockTecnicoNuevo,
                        2
                    )
                ),
        ]);

    });

    return redirect()
        ->route(
            'inventario.show',
            $material
        )
        ->with(
            'success',
            'Material asignado al técnico correctamente.'
        );
}

    public function createMovimiento(Material $material)
    {
        return view('inventario.movimiento', compact('material'));
    }

    public function storeMovimiento(Request $request, Material $material)
    {
        $datos = $request->validate([
            'tipo_movimiento' => 'required|in:Entrada,Salida,Ajuste,Devolución',
            'cantidad' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($datos, $material) {

            $stockAnterior = $material->stock_actual;

            switch ($datos['tipo_movimiento']) {

                case 'Entrada':
                case 'Devolución':
                    $stockNuevo = $stockAnterior + $datos['cantidad'];
                    break;

                case 'Salida':

                    if ($datos['cantidad'] > $stockAnterior) {
                        abort(422, 'No hay suficiente stock disponible.');
                    }

                    $stockNuevo = $stockAnterior - $datos['cantidad'];
                    break;

                case 'Ajuste':
                    $stockNuevo = $datos['cantidad'];
                    break;
            }

            $material->update([
                'stock_actual' => $stockNuevo,
            ]);

            MovimientoInventario::create([
                'material_id' => $material->id,
                'tecnico_id' => null,
                'orden_trabajo_id' => null,
                'registrado_por' => auth()->id(),
                'tipo_movimiento' => $datos['tipo_movimiento'],
                'cantidad' => $datos['cantidad'],
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'fecha_movimiento' => now(),
                'observaciones' => $datos['observaciones'] ?? null,
            ]);
        });

            return redirect()
                ->route('inventario.show', $material)
                ->with('success', 'Movimiento registrado correctamente.');
        }
                    public function edit(Material $material)
        {
            return view('inventario.edit', compact('material'));
        }


    public function update(Request $request, Material $material)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50|unique:materiales,codigo,' . $material->id,
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'stock_minimo' => 'required|numeric|min:0',
            'costo_unitario' => 'required|numeric|min:0',
            'estado' => 'required|boolean',
        ]);

        $material->update($datos);

        return redirect()
            ->route('inventario.show', $material)
            ->with('success', 'Material actualizado correctamente.');
    }
    public function tecnicos(Request $request)
    {
        // ==========================================
        // SEGURIDAD
        // ==========================================
        $rol = auth()->user()?->role?->nombre;

        if (!in_array(
            $rol,
            ['Administrador', 'Recepcion'],
            true
        )) {
            abort(
                403,
                'No tiene permiso para consultar el inventario de técnicos.'
            );
        }


        // ==========================================
        // CONSULTA DE TÉCNICOS
        // ==========================================
        $query = Tecnico::with('user')
            ->withCount([
                'inventarios as materiales_asignados' => function ($q) {
                    $q->where('cantidad', '>', 0);
                }
            ])
            ->where('estado', true);


        // ==========================================
        // BUSCADOR
        // ==========================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'especialidad',
                    'like',
                    '%' . $buscar . '%'
                );

                $q->orWhereHas(
                    'user',
                    function ($usuario) use ($buscar) {

                        $usuario->where(
                            'name',
                            'like',
                            '%' . $buscar . '%'
                        );
                    }
                );
            });
        }


        $tecnicos = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();


        // ==========================================
        // INDICADORES
        // ==========================================
        $totalTecnicos = Tecnico::where(
                'estado',
                true
            )
            ->count();


        $tecnicosConInventario = InventarioTecnico::where(
                'cantidad',
                '>',
                0
            )
            ->distinct()
            ->count('tecnico_id');


        $tecnicosSinInventario = max(
            0,
            $totalTecnicos - $tecnicosConInventario
        );

        $materialesAsignados = InventarioTecnico::where(
                'cantidad',
                '>',
                0
            )
            ->distinct()
            ->count('material_id');


        return view(
            'inventario.tecnicos',
            compact(
                'tecnicos',
                'totalTecnicos',
                'tecnicosConInventario',
                'tecnicosSinInventario',
                'materialesAsignados'
            )
        );
    }

    public function tecnicoDetalle(Request $request, Tecnico $tecnico
    ) {
        // ==========================================
        // SEGURIDAD
        // ==========================================
        $rol = auth()->user()?->role?->nombre;

        if (!in_array(
            $rol,
            ['Administrador', 'Recepcion'],
            true
        )) {
            abort(
                403,
                'No tiene permiso para consultar este inventario.'
            );
        }

        $tecnico->load('user');

        // ==========================================
        // INVENTARIO DEL TÉCNICO
        // ==========================================
        $query = InventarioTecnico::with('material')
            ->where(
                'tecnico_id',
                $tecnico->id
            );

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->whereHas(
                'material',
                function ($q) use ($buscar) {

                    $q->where(
                        'nombre',
                        'like',
                        '%' . $buscar . '%'
                    )
                    ->orWhere(
                        'codigo',
                        'like',
                        '%' . $buscar . '%'
                    );
                }
            );
        }

        $inventarioTecnico = $query
            ->orderByDesc('cantidad')
            ->paginate(10)
            ->withQueryString();

        $totalMateriales = InventarioTecnico::where(
                'tecnico_id',
                $tecnico->id
            )
            ->where(
                'cantidad',
                '>',
                0
            )
            ->count();


        return view(
            'inventario.tecnico-detalle',
            compact(
                'tecnico',
                'inventarioTecnico',
                'totalMateriales'
            )
        );
    }
    public function movimientos(Request $request)
    {
        // =====================================================
        // SEGURIDAD
        // =====================================================
        $rol = auth()->user()?->role?->nombre;

        if (!in_array(
            $rol,
            ['Administrador', 'Recepcion'],
            true
        )) {
            abort(
                403,
                'No tiene permiso para consultar los movimientos de inventario.'
            );
        }


        // =====================================================
        // CONSULTA PRINCIPAL
        // =====================================================
        $query = MovimientoInventario::with([
            'material',
            'tecnico.user',
            'ordenTrabajo',
            'usuarioRegistro',
        ]);


        // =====================================================
        // BUSCAR
        // Material / código / orden / técnico
        // =====================================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->whereHas(
                    'material',
                    function ($material) use ($buscar) {

                        $material
                            ->where(
                                'nombre',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'codigo',
                                'like',
                                '%' . $buscar . '%'
                            );
                    }
                );

                $q->orWhereHas(
                    'ordenTrabajo',
                    function ($orden) use ($buscar) {

                        $orden->where(
                            'codigo',
                            'like',
                            '%' . $buscar . '%'
                        );
                    }
                );

                $q->orWhereHas(
                    'tecnico.user',
                    function ($usuario) use ($buscar) {

                        $usuario->where(
                            'name',
                            'like',
                            '%' . $buscar . '%'
                        );
                    }
                );
            });
        }


        // =====================================================
        // FILTRO POR TIPO OPERATIVO
        // =====================================================
        if ($request->filled('tipo')) {

            switch ($request->tipo) {

                case 'Entrada':

                    $query->where(
                        'tipo_movimiento',
                        'Entrada'
                    );

                    break;


                case 'Asignacion':

                    $query
                        ->where(
                            'tipo_movimiento',
                            'Salida'
                        )
                        ->whereNotNull(
                            'tecnico_id'
                        )
                        ->whereNull(
                            'orden_trabajo_id'
                        );

                    break;


                case 'Consumo':

                    $query->where(function ($q) {

                        $q->where(
                            'tipo_movimiento',
                            'Consumo'
                        );

                        $q->orWhere(function ($salida) {

                            $salida
                                ->where(
                                    'tipo_movimiento',
                                    'Salida'
                                )
                                ->whereNotNull(
                                    'tecnico_id'
                                )
                                ->whereNotNull(
                                    'orden_trabajo_id'
                                );
                        });
                    });

                    break;


                case 'Salida':

                    $query
                        ->where(
                            'tipo_movimiento',
                            'Salida'
                        )
                        ->whereNull(
                            'tecnico_id'
                        )
                        ->whereNull(
                            'orden_trabajo_id'
                        );

                    break;


                case 'Ajuste':

                    $query->where(
                        'tipo_movimiento',
                        'Ajuste'
                    );

                    break;


                case 'Devolucion':

                    $query->whereIn(
                        'tipo_movimiento',
                        [
                            'Devolución',
                            'Devolucion',
                        ]
                    );

                    break;
            }
        }


        // =====================================================
        // FILTRO TÉCNICO
        // =====================================================
        if ($request->filled('tecnico')) {

            $query->where(
                'tecnico_id',
                $request->tecnico
            );
        }


        // =====================================================
        // FILTRO FECHA
        // =====================================================
        if ($request->filled('fecha')) {

            $query->whereDate(
                'fecha_movimiento',
                $request->fecha
            );
        }


        // =====================================================
        // LISTADO
        // =====================================================
        $movimientos = $query
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();


        // =====================================================
        // INDICADORES
        // =====================================================

        $movimientosHoy = MovimientoInventario::whereDate(
                'fecha_movimiento',
                now()->toDateString()
            )
            ->count();


        $entradas = MovimientoInventario::where(
                'tipo_movimiento',
                'Entrada'
            )
            ->count();


        $asignaciones = MovimientoInventario::where(
                'tipo_movimiento',
                'Salida'
            )
            ->whereNotNull(
                'tecnico_id'
            )
            ->whereNull(
                'orden_trabajo_id'
            )
            ->count();


        $consumos = MovimientoInventario::where(function ($q) {

                $q->where(
                    'tipo_movimiento',
                    'Consumo'
                );

                $q->orWhere(function ($salida) {

                    $salida
                        ->where(
                            'tipo_movimiento',
                            'Salida'
                        )
                        ->whereNotNull(
                            'tecnico_id'
                        )
                        ->whereNotNull(
                            'orden_trabajo_id'
                        );
                });

            })
            ->count();


        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->orderBy('id')
            ->get();


        return view(
            'inventario.movimientos',
            compact(
                'movimientos',
                'movimientosHoy',
                'entradas',
                'asignaciones',
                'consumos',
                'tecnicos'
            )
        );
    }
}

