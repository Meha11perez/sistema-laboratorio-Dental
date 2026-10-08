<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Clinica;
use App\Models\DetalleMensajeria;
use App\Models\Devolucion;
use App\Models\Garantia;
use App\Models\InventarioTecnico;
use App\Models\Material;
use App\Models\MovimientoInventario;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\EstadoOrden;
use App\Models\OrdenTrabajo;
use App\Models\Tecnico;
use App\Models\TipoProtesis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReporteAdicionalController extends Controller
{
    private const AREAS = [
        'removible' => 'Removibles', 'fija' => 'Fijas',
        'cromo_cobalto' => 'Cromo Cobalto', 'ortodoncia' => 'Ortodoncia',
    ];

    public function index(Request $request, string $reporte)
    {
        $datos = $this->datos($request, $reporte);
        $filas = (clone $datos['consulta'])->paginate(15)->withQueryString()
            ->through($datos['mapear']);

        return view('reportes.adicional', compact('datos', 'filas', 'reporte'));
    }

    public function imprimir(Request $request, string $reporte)
    {
        $datos = $this->datos($request, $reporte);
        $filas = (clone $datos['consulta'])->get()->map($datos['mapear']);

        return view('reportes.imprimir-adicional', compact('datos', 'filas', 'reporte'));
    }

    public function exportar(Request $request, string $reporte)
    {
        $datos = $this->datos($request, $reporte);
        $sufijo = $datos['filtros']['mes'] ?? ($datos['filtros']['desde'] ?? 'actual');
        $nombre = $reporte.'_'.($datos['filtros']['vista'] ?? 'resumen').'_'.$sufijo.'.csv';

        return response()->streamDownload(function () use ($datos) {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, array_column($datos['columnas'], 'etiqueta'), ';', '"', '');
            foreach ((clone $datos['consulta'])->lazy(200) as $registro) {
                $fila = ($datos['mapear'])($registro);
                $celdas = [];
                foreach ($datos['columnas'] as $columna) {
                    $celdas[] = $this->celdaCsv($fila[$columna['campo']] ?? '');
                }
                fputcsv($archivo, $celdas, ';', '"', '');
            }
            fclose($archivo);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function datos(Request $request, string $reporte): array
    {
        return match ($reporte) {
            'inventario' => $this->inventario($request),
            'calidad' => $this->calidad($request),
            'pagos' => $this->pagos($request),
            'mensajeria' => $this->mensajeria($request),
            'odontologos' => $this->odontologos($request),
            'pacientes' => $this->pacientes($request),
            default => abort(404),
        };
    }

    private function filtros(Request $request, array $reglas, string $periodo = 'fechas'): array
    {
        $hoy = now('America/Guatemala');
        if ($periodo === 'fechas') {
            $request->mergeIfMissing([
                'desde' => $hoy->copy()->startOfMonth()->toDateString(),
                'hasta' => $hoy->copy()->endOfMonth()->toDateString(),
            ]);
            $reglas += [
                'desde' => ['required', 'date_format:Y-m-d'],
                'hasta' => ['required', 'date_format:Y-m-d', 'after_or_equal:desde'],
            ];
        } elseif ($periodo === 'mes') {
            $request->mergeIfMissing(['mes' => $hoy->format('Y-m')]);
            $reglas += ['mes' => ['required', 'date_format:Y-m']];
        }
        return $request->validate($reglas);
    }

    private function vista(Request $request, array $opciones, string $predeterminada): string
    {
        $request->mergeIfMissing(['vista' => $predeterminada]);
        return $request->validate(['vista' => ['required', Rule::in(array_keys($opciones))]])['vista'];
    }

    private function existe(string $modelo): array
    {
        return ['nullable', 'integer', Rule::exists((new $modelo)->getTable(), 'id')];
    }

    private function fechas($query, string $campo, array $filtros)
    {
        return $query->whereDate($campo, '>=', $filtros['desde'])
            ->whereDate($campo, '<=', $filtros['hasta']);
    }

    private function camposPeriodo(string $etiqueta = 'Fecha', string $periodo = 'fechas'): array
    {
        if ($periodo === 'ninguno') {
            return [];
        }
        if ($periodo === 'mes') {
            return [['nombre' => 'mes', 'etiqueta' => 'Mes del reporte', 'tipo' => 'month']];
        }
        return [
            ['nombre' => 'desde', 'etiqueta' => $etiqueta.' desde', 'tipo' => 'date'],
            ['nombre' => 'hasta', 'etiqueta' => $etiqueta.' hasta', 'tipo' => 'date'],
        ];
    }

    private function select(string $nombre, string $etiqueta, array $opciones, bool $todos = true): array
    {
        return compact('nombre', 'etiqueta', 'opciones', 'todos') + ['tipo' => 'select'];
    }

    private function lista(array $valores): array
    {
        return array_combine($valores, $valores);
    }

    private function catalogoTecnicos(): array
    {
        return Tecnico::with('user')->get()->sortBy(fn ($t) => $t->user?->name ?? '')
            ->mapWithKeys(fn ($t) => [$t->id => $t->user?->name ?? 'Técnico #'.$t->id])->all();
    }

    private function camposClientes(): array
    {
        return [
            $this->select('odontologo_id', 'Odontólogo', Odontologo::orderBy('nombre')->pluck('nombre', 'id')->all()),
            $this->select('clinica_id', 'Clínica', Clinica::orderBy('nombre')->pluck('nombre', 'id')->all()),
        ];
    }

    private function reglasClientes(): array
    {
        return ['odontologo_id' => $this->existe(Odontologo::class), 'clinica_id' => $this->existe(Clinica::class)];
    }

    private function filtrarCliente($query, array $filtros, string $relacion = 'odontologo')
    {
        if (!empty($filtros['odontologo_id'])) {
            $query->where('odontologo_id', $filtros['odontologo_id']);
        }
        if (!empty($filtros['clinica_id'])) {
            $query->whereHas($relacion, fn ($q) => $q->where('clinica_id', $filtros['clinica_id']));
        }
        return $query;
    }

    private function columnas(array $etiquetas, array $monedas = [], array $decimales = []): array
    {
        $columnas = [];
        foreach ($etiquetas as $campo => $etiqueta) {
            $formato = in_array($campo, $monedas, true) ? 'moneda'
                : (in_array($campo, $decimales, true) ? 'decimal' : 'texto');
            $columnas[] = compact('campo', 'etiqueta', 'formato');
        }
        return $columnas;
    }

    private function metrica(string $etiqueta, $valor, string $formato = 'numero'): array
    {
        return compact('etiqueta', 'valor', 'formato');
    }

    private function base(string $titulo, string $subtitulo, array $filtros, array $campos,
        $consulta, array $columnas, callable $mapear, array $metricas, array $notas): array
    {
        $filtros = array_intersect_key($filtros, array_fill_keys(array_column($campos, 'nombre'), true));
        return compact('titulo', 'subtitulo', 'filtros', 'campos', 'consulta', 'columnas', 'mapear', 'metricas', 'notas');
    }

    private function codigoOrden($orden): string
    {
        return $orden ? ($orden->codigo_area ?: $orden->codigo) : 'Sin orden asociada';
    }

    private function celdaCsv($valor): string
    {
        $texto = (string) $valor;
        if (preg_match('/^\s*[=+\-@]/u', $texto) || preg_match('/^[\t\r\n]/', $texto)) {
            return "'".$texto;
        }
        return $texto;
    }

    private function inventario(Request $request): array
    {
        $vistas = ['existencias' => 'Existencias actuales', 'movimientos' => 'Movimientos del período', 'asignaciones' => 'Inventario actual por técnico'];
        $vista = $this->vista($request, $vistas, 'existencias');
        $periodo = $vista === 'movimientos' ? 'fechas' : 'ninguno';
        $tipos = $this->lista(['Entrada', 'Salida', 'Consumo', 'Ajuste', 'Devolución']);
        $filtros = $this->filtros($request, [
            'vista' => ['required', Rule::in(array_keys($vistas))],
            'material_id' => $this->existe(Material::class),
            'tecnico_id' => $this->existe(Tecnico::class),
            'tipo_movimiento' => ['nullable', Rule::in(array_keys($tipos))],
            'estado_material' => ['nullable', Rule::in(['activo', 'inactivo'])],
            'stock' => ['nullable', Rule::in(['bajo', 'agotado', 'suficiente'])],
        ], $periodo);
        $campos = [
            $this->select('vista', 'Consulta', $vistas, false),
            ...$this->camposPeriodo('Movimiento', $periodo),
            $this->select('material_id', 'Material', Material::orderBy('nombre')->pluck('nombre', 'id')->all()),
        ];
        if ($vista !== 'existencias') {
            $campos[] = $this->select('tecnico_id', 'Técnico', $this->catalogoTecnicos());
        }
        if ($vista === 'movimientos') {
            $campos[] = $this->select('tipo_movimiento', 'Tipo de movimiento', $tipos);
            $query = $this->fechas(MovimientoInventario::query(), 'fecha_movimiento', $filtros);
            foreach (['material_id', 'tecnico_id', 'tipo_movimiento'] as $campo) {
                if (!empty($filtros[$campo])) {
                    $query->where($campo, $filtros[$campo]);
                }
            }
            $metricas = [
                $this->metrica('Movimientos registrados', (clone $query)->count()),
                $this->metrica('Materiales con movimientos', (clone $query)->distinct()->count('material_id')),
                $this->metrica('Movimientos de consumo', (clone $query)->where('tipo_movimiento', 'Consumo')->count()),
            ];
            $query->with(['material', 'tecnico.user', 'ordenTrabajo'])->orderByDesc('fecha_movimiento')->orderByDesc('id');
            $columnas = $this->columnas([
                'fecha' => 'Movimiento', 'material' => 'Material', 'unidad' => 'Unidad', 'tipo' => 'Tipo',
                'cantidad' => 'Cantidad registrada', 'anterior' => 'Stock anterior registrado',
                'nuevo' => 'Stock nuevo registrado', 'tecnico' => 'Técnico', 'orden' => 'Orden', 'observaciones' => 'Observaciones',
            ], [], ['cantidad', 'anterior', 'nuevo']);
            $mapear = fn ($r) => [
                'fecha' => $r->fecha_movimiento?->format('d/m/Y H:i'), 'material' => $r->material?->nombre ?? 'Sin material',
                'unidad' => $r->material?->unidad_medida, 'tipo' => $r->tipo_movimiento, 'cantidad' => $r->cantidad,
                'anterior' => $r->stock_anterior, 'nuevo' => $r->stock_nuevo,
                'tecnico' => $r->tecnico?->user?->name ?? 'Sin técnico asociado',
                'orden' => $this->codigoOrden($r->ordenTrabajo), 'observaciones' => $r->observaciones,
            ];
            $notas = ['El período usa la fecha del movimiento. Los stocks anterior y nuevo se muestran tal como fueron registrados; pueden corresponder a bodega o al técnico.', 'Las cantidades no se suman entre materiales de unidades distintas. Un Ajuste registra el stock fijado, no necesariamente una entrada de esa cantidad.'];
        } elseif ($vista === 'asignaciones') {
            $query = InventarioTecnico::query()->join('materiales as m', 'm.id', '=', 'inventarios_tecnicos.material_id')
                ->select('inventarios_tecnicos.*')->where('inventarios_tecnicos.cantidad', '>', 0);
            foreach (['material_id', 'tecnico_id'] as $campo) {
                if (!empty($filtros[$campo])) {
                    $query->where('inventarios_tecnicos.'.$campo, $filtros[$campo]);
                }
            }
            $metricas = [
                $this->metrica('Asignaciones con existencia', (clone $query)->count()),
                $this->metrica('Técnicos con existencia', (clone $query)->distinct()->count('inventarios_tecnicos.tecnico_id')),
                $this->metrica('Valor aproximado asignado', (clone $query)->sum(DB::raw('inventarios_tecnicos.cantidad * m.costo_unitario')), 'moneda'),
            ];
            $query->with(['material', 'tecnico.user'])->orderBy('inventarios_tecnicos.tecnico_id')->orderBy('inventarios_tecnicos.id');
            $columnas = $this->columnas(['tecnico' => 'Técnico', 'material' => 'Material', 'unidad' => 'Unidad', 'cantidad' => 'Existencia asignada', 'valor' => 'Valor aproximado'], ['valor'], ['cantidad']);
            $mapear = fn ($r) => [
                'tecnico' => $r->tecnico?->user?->name ?? 'Técnico #'.$r->tecnico_id, 'material' => $r->material?->nombre,
                'unidad' => $r->material?->unidad_medida, 'cantidad' => $r->cantidad,
                'valor' => round((float) $r->cantidad * (float) ($r->material?->costo_unitario ?? 0), 2),
            ];
            $notas = ['Estas son existencias actuales, sin filtro de fechas. Incluyen técnicos desactivados que conservan material.', 'La valoración es aproximada y utiliza el costo unitario actual de cada material.'];
        } else {
            $campos[] = $this->select('estado_material', 'Estado del material', ['activo' => 'Activo', 'inactivo' => 'Inactivo']);
            $campos[] = $this->select('stock', 'Existencia de bodega', ['bajo' => 'En mínimo o por debajo', 'agotado' => 'Agotado', 'suficiente' => 'Por encima del mínimo']);
            $asignados = InventarioTecnico::select('material_id')->selectRaw('SUM(cantidad) AS cantidad_asignada')->groupBy('material_id');
            $query = Material::query()->leftJoinSub($asignados, 'asignados', 'asignados.material_id', '=', 'materiales.id')
                ->select('materiales.*')->selectRaw('COALESCE(asignados.cantidad_asignada, 0) AS cantidad_asignada');
            if (!empty($filtros['material_id'])) {
                $query->where('materiales.id', $filtros['material_id']);
            }
            if (!empty($filtros['estado_material'])) {
                $query->where('materiales.estado', $filtros['estado_material'] === 'activo');
            }
            if (($filtros['stock'] ?? '') === 'bajo') {
                $query->whereColumn('materiales.stock_actual', '<=', 'materiales.stock_minimo');
            } elseif (($filtros['stock'] ?? '') === 'agotado') {
                $query->where('materiales.stock_actual', '<=', 0);
            } elseif (($filtros['stock'] ?? '') === 'suficiente') {
                $query->whereColumn('materiales.stock_actual', '>', 'materiales.stock_minimo');
            }
            $metricas = [
                $this->metrica('Materiales encontrados', (clone $query)->count()),
                $this->metrica('En mínimo o por debajo', (clone $query)->whereColumn('materiales.stock_actual', '<=', 'materiales.stock_minimo')->count()),
                $this->metrica('Valor aproximado en bodega', (clone $query)->sum(DB::raw('materiales.stock_actual * materiales.costo_unitario')), 'moneda'),
            ];
            $query->orderBy('materiales.nombre')->orderBy('materiales.id');
            $columnas = $this->columnas([
                'codigo' => 'Código', 'material' => 'Material', 'unidad' => 'Unidad', 'bodega' => 'Existencia en bodega',
                'minimo' => 'Mínimo de bodega', 'asignado' => 'Asignado a técnicos', 'estado' => 'Estado',
                'alerta' => 'Alerta de bodega', 'valor' => 'Valor aproximado en bodega',
            ], ['valor'], ['bodega', 'minimo', 'asignado']);
            $mapear = fn ($r) => [
                'codigo' => $r->codigo, 'material' => $r->nombre, 'unidad' => $r->unidad_medida,
                'bodega' => $r->stock_actual, 'minimo' => $r->stock_minimo, 'asignado' => $r->cantidad_asignada,
                'estado' => $r->estado ? 'Activo' : 'Inactivo',
                'alerta' => (float) $r->stock_actual <= (float) $r->stock_minimo ? 'Reponer' : 'Suficiente',
                'valor' => round((float) $r->stock_actual * (float) $r->costo_unitario, 2),
            ];
            $notas = ['Existencias actuales: no representan el stock de un mes anterior. Las alertas comparan únicamente la existencia de bodega con su mínimo.', 'El material asignado está separado de bodega. La valoración usa costos actuales y es aproximada.'];
        }
        return $this->base('Inventario', $vistas[$vista], $filtros, $campos, $query, $columnas, $mapear, $metricas, $notas);
    }


    private function calidad(Request $request): array
    {
        $vistas = ['devoluciones' => 'Devoluciones registradas', 'garantias' => 'Garantías por inicio de cobertura', 'repeticiones' => 'Órdenes de repetición'];
        $vista = $this->vista($request, $vistas, 'devoluciones');
        $estados = $vista === 'garantias'
            ? $this->lista(['Vigente', 'Por vencer', 'Vencida', 'Aplicada', 'Anulada'])
            : $this->lista(['Registrada', 'En revisión', 'En corrección', 'Resuelta', 'Rechazada']);
        $tipos = $this->lista(['Devolución', 'Repetición', 'Corrección']);
        $filtros = $this->filtros($request, $this->reglasClientes() + [
            'vista' => ['required', Rule::in(array_keys($vistas))],
            'area_trabajo' => ['nullable', Rule::in(array_keys(self::AREAS))],
            'tipo_protesis_id' => $this->existe(TipoProtesis::class),
            'tipo' => ['nullable', Rule::in(array_keys($tipos))],
            'estado' => ['nullable', Rule::in(array_keys($estados))],
            'tecnico_responsable_id' => $this->existe(Tecnico::class),
        ]);
        $fecha = match ($vista) { 'garantias' => 'Inicio de garantía', 'repeticiones' => 'Ingreso', default => 'Devolución' };
        $campos = [
            $this->select('vista', 'Consulta', $vistas, false), ...$this->camposPeriodo($fecha), ...$this->camposClientes(),
            $this->select('area_trabajo', 'Área de la orden', self::AREAS),
            $this->select('tipo_protesis_id', 'Tipo de prótesis', TipoProtesis::orderBy('nombre')->pluck('nombre', 'id')->all()),
        ];
        $filtrarOrden = function ($orden) use ($filtros) {
            $this->filtrarCliente($orden, $filtros);
            foreach (['area_trabajo', 'tipo_protesis_id'] as $campo) {
                if (!empty($filtros[$campo])) {
                    $orden->where($campo, $filtros[$campo]);
                }
            }
        };
        if ($vista === 'repeticiones') {
            $query = $this->fechas(OrdenTrabajo::where('tipo_orden', 'Repeticion'), 'fecha_ingreso', $filtros);
            $filtrarOrden($query);
            $metricas = [
                $this->metrica('Órdenes de repetición', (clone $query)->count()),
                $this->metrica('Odontólogos asociados', (clone $query)->distinct()->count('odontologo_id')),
                $this->metrica('Valor registrado de repeticiones', (clone $query)->sum('total'), 'moneda'),
            ];
            $query->with(['odontologo', 'ordenOrigen', 'estadoOrden', 'tipoProtesis'])->orderByDesc('fecha_ingreso')->orderByDesc('id');
            $columnas = $this->columnas([
                'fecha' => 'Ingreso', 'orden' => 'Repetición', 'origen' => 'Orden original', 'doctor' => 'Odontólogo',
                'area' => 'Área', 'protesis' => 'Prótesis', 'motivo' => 'Motivo', 'estado' => 'Estado actual', 'total' => 'Valor registrado',
            ], ['total']);
            $mapear = fn ($r) => [
                'fecha' => $r->fecha_ingreso?->format('d/m/Y'), 'orden' => $this->codigoOrden($r),
                'origen' => $this->codigoOrden($r->ordenOrigen), 'doctor' => $r->odontologo?->nombre,
                'area' => self::AREAS[$r->area_trabajo] ?? 'Área por revisar', 'protesis' => $r->tipoProtesis?->nombre,
                'motivo' => $r->motivo_repeticion, 'estado' => $r->estadoOrden?->nombre, 'total' => $r->total,
            ];
            $notas = ['El período usa la fecha de ingreso de las órdenes cuyo tipo es Repeticion. Incluye todos sus estados, también las canceladas.', 'El valor registrado no representa dinero cobrado ni pérdida por garantía. Algunas repeticiones pueden tener precio cero.'];
        } elseif ($vista === 'garantias') {
            $campos[] = $this->select('estado', 'Estado registrado de la garantía', $estados);
            $query = $this->fechas(Garantia::query(), 'fecha_inicio', $filtros)->whereHas('ordenTrabajo', $filtrarOrden);
            if (!empty($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
            // Respeta Aplicada y Anulada; el calendario no reactiva esas garantías.
            $hoy = now('America/Guatemala')->toDateString();
            $limite = now('America/Guatemala')->addDays(15)->toDateString();
            $vigencia = "CASE WHEN estado IN ('Aplicada', 'Anulada') THEN estado WHEN fecha_inicio IS NULL OR fecha_vencimiento IS NULL THEN 'Sin fechas' WHEN fecha_inicio > ? THEN 'No iniciada' WHEN fecha_vencimiento < ? THEN 'Vencida' WHEN fecha_vencimiento <= ? THEN 'Por vencer' ELSE 'Vigente' END";
            $parametros = [$hoy, $hoy, $limite];
            $metricas = [
                $this->metrica('Garantías encontradas', (clone $query)->count()),
                $this->metrica('Por vencer hoy o en 15 días', (clone $query)->whereRaw('('.$vigencia.') = ?', [...$parametros, 'Por vencer'])->count()),
                $this->metrica('Vencidas actualmente', (clone $query)->whereRaw('('.$vigencia.') = ?', [...$parametros, 'Vencida'])->count()),
            ];
            $query->select('garantias.*')->selectRaw($vigencia.' AS vigencia_calculada', $parametros)
                ->with(['ordenTrabajo.odontologo'])->orderByDesc('fecha_inicio')->orderByDesc('id');
            $columnas = $this->columnas([
                'orden' => 'Orden', 'doctor' => 'Odontólogo', 'inicio' => 'Inicio', 'vence' => 'Vencimiento',
                'estado' => 'Estado registrado', 'vigencia' => 'Situación actual', 'observaciones' => 'Observaciones',
            ]);
            $mapear = fn ($r) => [
                'orden' => $this->codigoOrden($r->ordenTrabajo), 'doctor' => $r->ordenTrabajo?->odontologo?->nombre,
                'inicio' => $r->fecha_inicio?->format('d/m/Y'), 'vence' => $r->fecha_vencimiento?->format('d/m/Y'),
                'estado' => $r->estado, 'vigencia' => $r->vigencia_calculada, 'observaciones' => $r->observaciones,
            ];
            $notas = ['El período selecciona garantías por su fecha de inicio. Su situación se calcula a la fecha de consulta en Guatemala; no es un estado histórico al cierre del período.', 'Las garantías Aplicada y Anulada conservan su condición. Por vencer incluye las que vencen hoy o dentro de 15 días.'];
        } else {
            $campos[] = $this->select('tipo', 'Tipo de incidencia', $tipos);
            $campos[] = $this->select('estado', 'Estado registrado', $estados);
            $campos[] = $this->select('tecnico_responsable_id', 'Técnico responsable registrado', $this->catalogoTecnicos());
            $query = $this->fechas(Devolucion::query(), 'fecha_devolucion', $filtros)->whereHas('ordenTrabajo', $filtrarOrden);
            foreach (['tipo', 'estado', 'tecnico_responsable_id'] as $campo) {
                if (!empty($filtros[$campo])) {
                    $query->where($campo, $filtros[$campo]);
                }
            }
            $metricas = [
                $this->metrica('Devoluciones registradas', (clone $query)->count()),
                $this->metrica('Con orden de repetición', (clone $query)->whereHas('repeticion')->count()),
                $this->metrica('Pérdida estimada registrada', (clone $query)->sum('perdida_estimada'), 'moneda'),
            ];
            $query->with(['ordenTrabajo.odontologo', 'tecnicoResponsable.user', 'repeticion', 'garantia'])
                ->orderByDesc('fecha_devolucion')->orderByDesc('id');
            $columnas = $this->columnas([
                'fecha' => 'Devolución', 'orden' => 'Orden', 'doctor' => 'Odontólogo', 'tipo' => 'Tipo', 'motivo' => 'Motivo',
                'tecnico' => 'Técnico responsable registrado', 'estado' => 'Estado', 'requiere' => 'Requiere repetición',
                'repeticion' => 'Orden de repetición', 'garantia' => 'Garantía asociada', 'perdida' => 'Pérdida estimada',
            ], ['perdida']);
            $mapear = fn ($r) => [
                'fecha' => $r->fecha_devolucion?->format('d/m/Y H:i'), 'orden' => $this->codigoOrden($r->ordenTrabajo),
                'doctor' => $r->ordenTrabajo?->odontologo?->nombre, 'tipo' => $r->tipo, 'motivo' => $r->motivo,
                'tecnico' => $r->tecnicoResponsable?->user?->name ?? 'Sin responsable registrado', 'estado' => $r->estado,
                'requiere' => $r->requiere_repeticion ? 'Sí' : 'No',
                'repeticion' => $r->repeticion ? $this->codigoOrden($r->repeticion) : 'Sin repetición creada',
                'garantia' => $r->garantia_id ? '#'.$r->garantia_id : 'Sin garantía asociada', 'perdida' => $r->perdida_estimada,
            ];
            $notas = [''];
        }
        return $this->base('Garantías y devoluciones', $vistas[$vista], $filtros, $campos, $query, $columnas, $mapear, $metricas, $notas);
    }


    private const SALDO_SQL = 'CASE WHEN o.total > COALESCE(a.abonado, 0) THEN o.total - COALESCE(a.abonado, 0) ELSE 0 END';
    private const ESTADO_PAGO_SQL = "CASE WHEN o.total <= 0 THEN 'Sin precio' WHEN o.total <= COALESCE(a.abonado, 0) THEN 'Pagado' WHEN COALESCE(a.abonado, 0) > 0 THEN 'Parcial' ELSE 'Pendiente' END";

    private function consultaSaldos()
    {
        // Agrupa primero los abonos por orden: varios pagos o abonos no duplican su precio.
        $abonado = DB::table('abonos as b')->join('pagos as p', 'p.id', '=', 'b.pago_id')
            ->select('p.orden_trabajo_id')->selectRaw('SUM(b.monto) AS abonado')->groupBy('p.orden_trabajo_id');

        return DB::table('ordenes_trabajo as o')
            ->join('estados_orden as e', 'e.id', '=', 'o.estado_orden_id')
            ->join('odontologos as d', 'd.id', '=', 'o.odontologo_id')
            ->leftJoin('clinicas as c', 'c.id', '=', 'd.clinica_id')
            ->leftJoin('cuentas_odontologos as cuenta', 'cuenta.odontologo_id', '=', 'd.id')
            ->leftJoinSub($abonado, 'a', 'a.orden_trabajo_id', '=', 'o.id')
            ->where('e.nombre', '!=', 'Cancelado')
            ->select('o.id', 'o.odontologo_id', 'o.codigo', 'o.codigo_area', 'o.fecha_ingreso', 'o.total', 'd.clinica_id')
            ->addSelect('d.nombre as odontologo', 'c.nombre as clinica', 'cuenta.modalidad_pago')
            ->selectRaw('COALESCE(a.abonado, 0) AS abonado')
            ->selectRaw(self::SALDO_SQL.' AS saldo')
            ->selectRaw(self::ESTADO_PAGO_SQL.' AS estado_pago');
    }

    private function pagos(Request $request): array
    {
        $vistas = ['cobros' => 'Ingresos cobrados en el período', 'saldos' => 'Saldos actuales por orden'];
        $vista = $this->vista($request, $vistas, 'cobros');
        $periodo = $vista === 'cobros' ? 'fechas' : 'ninguno';
        $metodos = $this->lista(['Efectivo', 'Transferencia', 'Depósito', 'Otro']);
        $estados = $this->lista(['Sin precio', 'Pendiente', 'Parcial', 'Pagado']);
        $filtros = $this->filtros($request, $this->reglasClientes() + [
            'vista' => ['required', Rule::in(array_keys($vistas))],
            'metodo_pago' => ['nullable', Rule::in(array_keys($metodos))],
            'situacion' => ['nullable', Rule::in(array_keys($estados))],
            'alcance' => ['nullable', Rule::in(['pendientes', 'todos'])],
            'modalidad_pago' => ['nullable', Rule::in(['Contado', 'Semanal', 'Crédito', 'Sin cuenta'])],
        ], $periodo);
        $campos = [$this->select('vista', 'Consulta', $vistas, false), ...$this->camposPeriodo('Cobro', $periodo), ...$this->camposClientes()];
        if ($vista === 'cobros') {
            $campos[] = $this->select('metodo_pago', 'Método de pago', $metodos);
            $query = $this->fechas(Abono::query(), 'fecha_abono', $filtros);
            if (!empty($filtros['metodo_pago'])) {
                $query->where('metodo_pago', $filtros['metodo_pago']);
            }
            $query->whereHas('pago', fn ($pago) => $this->filtrarCliente($pago, $filtros));
            $metricas = [
                $this->metrica('Abonos registrados', (clone $query)->count()),
                $this->metrica('Pagos con abonos', (clone $query)->distinct()->count('pago_id')),
                $this->metrica('Cobrado durante el período', (clone $query)->sum('monto'), 'moneda'),
            ];
            $query->with(['pago.odontologo.clinica', 'pago.ordenTrabajo'])->orderByDesc('fecha_abono')->orderByDesc('id');
            $columnas = $this->columnas([
                'fecha' => 'Cobro', 'abono' => 'Abono', 'orden' => 'Orden', 'doctor' => 'Odontólogo del pago',
                'clinica' => 'Clínica', 'monto' => 'Monto cobrado', 'metodo' => 'Método', 'referencia' => 'Referencia', 'observaciones' => 'Observaciones',
            ], ['monto']);
            $mapear = fn ($r) => [
                'fecha' => $r->fecha_abono?->format('d/m/Y H:i'), 'abono' => '#'.$r->id,
                'orden' => $this->codigoOrden($r->pago?->ordenTrabajo), 'doctor' => $r->pago?->odontologo?->nombre,
                'clinica' => $r->pago?->odontologo?->clinica?->nombre ?? 'Sin clínica', 'monto' => $r->monto,
                'metodo' => $r->metodo_pago, 'referencia' => $r->referencia, 'observaciones' => $r->observaciones,
            ];
            $notas = ['Los ingresos se cuentan una vez por abono y según la fecha del abono. Pueden corresponder a órdenes de meses anteriores.', 'Se muestran todos los cobros registrados, incluso de órdenes actualmente canceladas. Este sistema no tiene un registro de reembolsos; el reporte no calcula ingresos netos de devoluciones ni utilidades.'];
        } else {
            $filtros['alcance'] = $filtros['alcance'] ?? 'pendientes';
            $campos[] = $this->select('alcance', 'Órdenes a consultar', ['pendientes' => 'Con saldo pendiente', 'todos' => 'Todas las no canceladas'], false);
            $campos[] = $this->select('situacion', 'Situación de cobro calculada', $estados);
            $campos[] = $this->select('modalidad_pago', 'Modalidad de la cuenta', $this->lista(['Contado', 'Semanal', 'Crédito', 'Sin cuenta']));
            $query = $this->consultaSaldos();
            if (!empty($filtros['odontologo_id'])) {
                $query->where('o.odontologo_id', $filtros['odontologo_id']);
            }
            if (!empty($filtros['clinica_id'])) {
                $query->where('d.clinica_id', $filtros['clinica_id']);
            }
            if (($filtros['modalidad_pago'] ?? '') === 'Sin cuenta') {
                $query->whereNull('cuenta.modalidad_pago');
            } elseif (!empty($filtros['modalidad_pago'])) {
                $query->where('cuenta.modalidad_pago', $filtros['modalidad_pago']);
            }
            if ($filtros['alcance'] === 'pendientes') {
                $query->whereRaw('('.self::SALDO_SQL.') > 0');
            }
            if (!empty($filtros['situacion'])) {
                $query->whereRaw('('.self::ESTADO_PAGO_SQL.') = ?', [$filtros['situacion']]);
            }
            $metricas = [
                $this->metrica('Órdenes encontradas', (clone $query)->count()),
                $this->metrica('Saldo pendiente actual', (clone $query)->sum(DB::raw(self::SALDO_SQL)), 'moneda'),
                $this->metrica('Órdenes sin precio definido', (clone $query)->where('o.total', '<=', 0)->count()),
            ];
            $query->orderBy('d.nombre')->orderBy('o.id');
            $columnas = $this->columnas([
                'doctor' => 'Odontólogo', 'clinica' => 'Clínica', 'modalidad' => 'Modalidad actual', 'orden' => 'Orden', 'ingreso' => 'Ingreso de orden',
                'total' => 'Valor actual del trabajo', 'abonado' => 'Abonado acumulado', 'saldo' => 'Saldo actual', 'situacion' => 'Situación calculada',
            ], ['total', 'abonado', 'saldo']);
            $mapear = fn ($r) => [
                'doctor' => $r->odontologo, 'clinica' => $r->clinica ?? 'Sin clínica', 'modalidad' => $r->modalidad_pago ?? 'Sin cuenta', 'orden' => $r->codigo_area ?: $r->codigo,
                'ingreso' => $r->fecha_ingreso ? Carbon::parse($r->fecha_ingreso)->format('d/m/Y') : '',
                'total' => $r->total, 'abonado' => $r->abonado, 'saldo' => $r->saldo, 'situacion' => $r->estado_pago,
            ];
            $notas = ['El saldo actual incluye órdenes de todos los meses y excluye las actualmente canceladas. No es un saldo histórico al cierre de un mes.', 'Se calcula por orden como su precio actual menos todos sus abonos, con mínimo cero. Incluye órdenes sin registro de pago. La consulta no sincroniza ni modifica pagos o cuentas.', 'Las órdenes con precio cero se identifican como Sin precio; el sistema no puede estimar su deuda. Para verlas, seleccione Todas las no canceladas.'];
        }
        return $this->base('Pagos y créditos', $vistas[$vista], $filtros, $campos, $query, $columnas, $mapear, $metricas, $notas);
    }

    private function odontologos(Request $request): array
    {
        $filtros = $this->filtros($request, $this->reglasClientes(), 'mes');
        $mes = Carbon::createFromFormat('!Y-m', $filtros['mes'], 'America/Guatemala');
        $periodo = ['desde' => $mes->copy()->startOfMonth()->toDateString(), 'hasta' => $mes->copy()->endOfMonth()->toDateString()];
        $campos = [...$this->camposPeriodo('Fecha', 'mes'), ...$this->camposClientes()];

        // Agregados separados para evitar multiplicar órdenes por cada abono.
        $ordenes = $this->fechas(DB::table('ordenes_trabajo as o')
            ->join('estados_orden as e', 'e.id', '=', 'o.estado_orden_id'), 'o.fecha_ingreso', $periodo)
            ->select('o.odontologo_id')->selectRaw('COUNT(*) AS registros')
            ->selectRaw("SUM(CASE WHEN e.nombre = 'Cancelado' THEN 1 ELSE 0 END) AS canceladas")
            ->selectRaw("SUM(CASE WHEN e.nombre != 'Cancelado' THEN o.total ELSE 0 END) AS valor_trabajos")
            ->groupBy('o.odontologo_id');
        $cobros = $this->fechas(DB::table('abonos as b')->join('pagos as p', 'p.id', '=', 'b.pago_id'), 'b.fecha_abono', $periodo)
            ->select('p.odontologo_id')->selectRaw('SUM(b.monto) AS cobrado')->groupBy('p.odontologo_id');
        $saldos = $this->consultaSaldos()->select('o.odontologo_id')
            ->selectRaw('SUM('.self::SALDO_SQL.') AS saldo_actual')->groupBy('o.odontologo_id');

        $query = DB::table('odontologos as d')->leftJoin('clinicas as c', 'c.id', '=', 'd.clinica_id')
            ->leftJoinSub($ordenes, 'n', 'n.odontologo_id', '=', 'd.id')
            ->leftJoinSub($cobros, 'i', 'i.odontologo_id', '=', 'd.id')
            ->leftJoinSub($saldos, 's', 's.odontologo_id', '=', 'd.id')
            ->select('d.id', 'd.nombre', 'd.codigo_cliente', 'c.nombre as clinica')
            ->selectRaw('COALESCE(n.registros, 0) AS registros, COALESCE(n.canceladas, 0) AS canceladas')
            ->selectRaw('COALESCE(n.valor_trabajos, 0) AS valor_trabajos, COALESCE(i.cobrado, 0) AS cobrado, COALESCE(s.saldo_actual, 0) AS saldo_actual');
        if (!empty($filtros['odontologo_id'])) {
            $query->where('d.id', $filtros['odontologo_id']);
        } else {
            $query->where(fn ($q) => $q->where('n.registros', '>', 0)->orWhere('i.cobrado', '>', 0)->orWhere('s.saldo_actual', '>', 0));
        }
        if (!empty($filtros['clinica_id'])) {
            $query->where('d.clinica_id', $filtros['clinica_id']);
        }
        $totales = DB::query()->fromSub(clone $query, 'r')->selectRaw('COALESCE(SUM(registros), 0) AS registros, COALESCE(SUM(valor_trabajos), 0) AS valor_trabajos, COALESCE(SUM(cobrado), 0) AS cobrado')->first();
        $metricas = [
            $this->metrica('Órdenes registradas en el mes', $totales->registros),
            $this->metrica('Valor de trabajos no cancelados', $totales->valor_trabajos, 'moneda'),
            $this->metrica('Cobrado durante el mes', $totales->cobrado, 'moneda'),
        ];
        $query->orderBy('d.nombre')->orderBy('d.id');
        $columnas = $this->columnas([
            'codigo' => 'Código de cliente', 'doctor' => 'Odontólogo', 'clinica' => 'Clínica', 'mes' => 'Mes',
            'ordenes' => 'Órdenes registradas', 'canceladas' => 'De ellas, canceladas', 'valor' => 'Valor de trabajos no cancelados',
            'cobrado' => 'Cobrado durante el mes', 'saldo' => 'Saldo actual de todos los meses',
        ], ['valor', 'cobrado', 'saldo']);
        $mapear = fn ($r) => [
            'codigo' => $r->codigo_cliente, 'doctor' => $r->nombre, 'clinica' => $r->clinica ?? 'Sin clínica', 'mes' => $mes->format('m/Y'),
            'ordenes' => $r->registros, 'canceladas' => $r->canceladas, 'valor' => $r->valor_trabajos,
            'cobrado' => $r->cobrado, 'saldo' => $r->saldo_actual,
        ];
        $notas = [
            ' ',
        ];
        $datos = $this->base('Resumen por odontólogo', 'Órdenes e ingresos del mes '.$mes->format('m/Y'), $filtros, $campos, $query, $columnas, $mapear, $metricas, $notas);
        $datos['enlaces'] = [
            ['texto' => 'Ver órdenes del mes', 'ruta' => 'reportes.produccion', 'parametros' => $periodo + $filtros],
            ['texto' => 'Ver cobros del mes', 'ruta' => 'reportes.pagos', 'parametros' => ['vista' => 'cobros'] + $periodo + $filtros],
        ];
        return $datos;
    }


    private function pacientes(Request $request): array
    {
        $vistas = ['resumen' => 'Resumen por paciente', 'ordenes' => 'Detalle de órdenes por paciente', 'directorio' => 'Listado actual de pacientes'];
        $vista = $this->vista($request, $vistas, 'resumen');
        if ($vista === 'directorio') {
            return $this->directorioPacientes($request, $vistas);
        }
        $filtros = $this->filtros($request, $this->reglasClientes() + [
            'vista' => ['required', Rule::in(array_keys($vistas))],
            'paciente_id' => $this->existe(Paciente::class),
            'estado_orden_id' => $this->existe(EstadoOrden::class),
        ]);
        $campos = [
            $this->select('vista', 'Consulta', $vistas, false),
            ...$this->camposPeriodo('Ingreso de orden'),
            $this->select('paciente_id', 'Paciente', Paciente::orderBy('nombre')->orderBy('apellido')->get()
                ->mapWithKeys(fn ($p) => [$p->id => trim($p->nombre.' '.$p->apellido).' (#'.$p->id.')'])->all()),
            ...$this->camposClientes(),
            $this->select('estado_orden_id', 'Estado actual de la orden', EstadoOrden::orderBy('nombre')->pluck('nombre', 'id')->all()),
        ];

        $filtrarOrden = function ($q) use ($filtros) {
            $this->fechas($q, 'fecha_ingreso', $filtros);
            $this->filtrarCliente($q, $filtros);
            foreach (['paciente_id', 'estado_orden_id'] as $campo) {
                if (!empty($filtros[$campo])) {
                    $q->where($campo, $filtros[$campo]);
                }
            }
        };
        $ordenes = OrdenTrabajo::query()->whereHas('paciente');
        $filtrarOrden($ordenes);
        $metricas = [
            $this->metrica('Pacientes con órdenes en el período', (clone $ordenes)->distinct()->count('paciente_id')),
            $this->metrica('Órdenes registradas en el período', (clone $ordenes)->count()),
            $this->metrica('De ellas, entregadas actualmente', (clone $ordenes)->whereHas('estadoOrden', fn ($e) => $e->where('nombre', 'Entregado'))->count()),
        ];

        if ($vista === 'ordenes') {
            $query = $ordenes->with(['paciente', 'odontologo', 'tipoProtesis', 'estadoOrden'])
                ->orderBy('paciente_id')->orderByDesc('fecha_ingreso')->orderByDesc('id');
            $columnas = $this->columnas([
                'paciente' => 'Paciente', 'doctor' => 'Odontólogo de la orden', 'orden' => 'Orden',
                'ingreso' => 'Ingreso', 'trabajo' => 'Trabajo', 'estado' => 'Estado actual',
                'estimada' => 'Entrega estimada', 'real' => 'Entrega real',
            ]);
            $mapear = fn ($o) => [
                'paciente' => trim($o->paciente?->nombre.' '.$o->paciente?->apellido),
                'doctor' => $o->odontologo?->nombre, 'orden' => $this->codigoOrden($o),
                'ingreso' => $o->fecha_ingreso?->format('d/m/Y'), 'trabajo' => $o->tipoProtesis?->nombre,
                'estado' => $o->estadoOrden?->nombre, 'estimada' => $o->fecha_entrega_estimada?->format('d/m/Y'),
                'real' => $o->fecha_entrega_real?->format('d/m/Y'),
            ];
        } else {
            $query = Paciente::with('odontologo')->whereHas('ordenesTrabajo', $filtrarOrden)
                ->withCount([
                    'ordenesTrabajo as ordenes_periodo' => $filtrarOrden,
                    'ordenesTrabajo as entregadas_periodo' => function ($q) use ($filtrarOrden) {
                        $filtrarOrden($q);
                        $q->whereHas('estadoOrden', fn ($e) => $e->where('nombre', 'Entregado'));
                    },
                    'ordenesTrabajo as canceladas_periodo' => function ($q) use ($filtrarOrden) {
                        $filtrarOrden($q);
                        $q->whereHas('estadoOrden', fn ($e) => $e->where('nombre', 'Cancelado'));
                    },
                ])->orderBy('nombre')->orderBy('apellido')->orderBy('id');
            $columnas = $this->columnas([
                'paciente' => 'Paciente', 'doctor' => 'Odontólogo actual del paciente',
                'telefono' => 'Teléfono', 'estado' => 'Estado del paciente', 'ordenes' => 'Órdenes del período',
                'entregadas' => 'De ellas, entregadas', 'canceladas' => 'De ellas, canceladas',
            ]);
            $mapear = fn ($p) => [
                'paciente' => trim($p->nombre.' '.$p->apellido).' (#'.$p->id.')',
                'doctor' => $p->odontologo?->nombre, 'telefono' => $p->telefono,
                'estado' => $p->estado ? 'Activo' : 'Inactivo', 'ordenes' => $p->ordenes_periodo,
                'entregadas' => $p->entregadas_periodo, 'canceladas' => $p->canceladas_periodo,
            ];
        }

        return $this->base('Reporte de pacientes', $vistas[$vista], $filtros, $campos,
            $query, $columnas, $mapear, $metricas, [
                '',
            ]);
    }

    private function directorioPacientes(Request $request, array $vistas): array
    {
        $filtros = $this->filtros($request, $this->reglasClientes() + [
            'vista' => ['required', Rule::in(['directorio'])],
            'paciente_id' => $this->existe(Paciente::class),
            'estado_paciente' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ], 'ninguno');
        $campos = [
            $this->select('vista', 'Consulta', $vistas, false),
            $this->select('paciente_id', 'Paciente', Paciente::orderBy('nombre')->orderBy('apellido')->get()
                ->mapWithKeys(fn ($p) => [$p->id => trim($p->nombre.' '.$p->apellido).' (#'.$p->id.')'])->all()),
            ...$this->camposClientes(),
            $this->select('estado_paciente', 'Estado del paciente', ['activo' => 'Activo', 'inactivo' => 'Inactivo']),
        ];
        $query = $this->filtrarCliente(Paciente::query(), $filtros);
        if (!empty($filtros['paciente_id'])) {
            $query->whereKey($filtros['paciente_id']);
        }
        if (!empty($filtros['estado_paciente'])) {
            $query->where('estado', $filtros['estado_paciente'] === 'activo');
        }
        $metricas = [
            $this->metrica('Pacientes encontrados', (clone $query)->count()),
            $this->metrica('Activos', (clone $query)->where('estado', true)->count()),
            $this->metrica('Inactivos', (clone $query)->where('estado', false)->count()),
        ];
        $query->with('odontologo')->orderBy('nombre')->orderBy('apellido')->orderBy('id');
        $columnas = $this->columnas([
            'paciente' => 'Paciente', 'doctor' => 'Odontólogo actual', 'telefono' => 'Teléfono',
            'estado' => 'Estado', 'registro' => 'Fecha de registro',
        ]);
        $mapear = fn ($p) => [
            'paciente' => trim($p->nombre.' '.$p->apellido).' (#'.$p->id.')', 'doctor' => $p->odontologo?->nombre,
            'telefono' => $p->telefono, 'estado' => $p->estado ? 'Activo' : 'Inactivo',
            'registro' => $p->created_at?->format('d/m/Y'),
        ];
        return $this->base('Reporte de pacientes', 'Listado actual de pacientes', $filtros, $campos,
            $query, $columnas, $mapear, $metricas, [
                'Incluye pacientes con o sin órdenes. Es el directorio actual y no utiliza un período de órdenes.',
                'Los filtros de odontólogo y clínica corresponden al odontólogo actual del paciente.',
            ]);
    }

    private function mensajeria(Request $request): array
    {
        $tipos = $this->lista(['Entrega', 'Recolección']);
        $estados = $this->lista(['Pendiente', 'Realizada', 'No realizada', 'Reprogramada']);
        $filtros = $this->filtros($request, $this->reglasClientes() + [
            'mensajero_id' => $this->existe(User::class),
            'tipo_movimiento' => ['nullable', Rule::in(array_keys($tipos))],
            'estado' => ['nullable', Rule::in(array_keys($estados))],
        ]);
        $mensajeros = User::where(fn ($q) => $q->whereHas('role', fn ($rol) => $rol->where('nombre', 'Mensajero'))->orWhereHas('rutasMensajeria'))
            ->orderBy('name')->pluck('name', 'id')->all();
        $campos = [
            ...$this->camposPeriodo('Ruta'), ...$this->camposClientes(),
            $this->select('mensajero_id', 'Mensajero', $mensajeros),
            $this->select('tipo_movimiento', 'Movimiento', $tipos),
            $this->select('estado', 'Estado de la visita', $estados),
        ];
        $query = DetalleMensajeria::whereHas('rutaMensajeria', function ($ruta) use ($filtros) {
            $this->fechas($ruta, 'fecha', $filtros);
            if (!empty($filtros['mensajero_id'])) {
                $ruta->where('mensajero_id', $filtros['mensajero_id']);
            }
        });
        foreach (['tipo_movimiento', 'estado'] as $campo) {
            if (!empty($filtros[$campo])) {
                $query->where($campo, $filtros[$campo]);
            }
        }
        if (!empty($filtros['odontologo_id'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('odontologo_id', $filtros['odontologo_id'])
                    ->orWhere(fn ($q) => $q->whereNull('odontologo_id')->whereHas('ordenTrabajo', fn ($o) => $o->where('odontologo_id', $filtros['odontologo_id'])));
            });
        }
        if (!empty($filtros['clinica_id'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('clinica_id', $filtros['clinica_id'])->orWhere(function ($q) use ($filtros) {
                    $q->whereNull('clinica_id')->where(function ($q) use ($filtros) {
                        $q->whereHas('odontologo', fn ($d) => $d->where('clinica_id', $filtros['clinica_id']))
                            ->orWhere(fn ($q) => $q->whereNull('odontologo_id')->whereHas('ordenTrabajo.odontologo', fn ($d) => $d->where('clinica_id', $filtros['clinica_id'])));
                    });
                });
            });
        }
        $metricas = [
            $this->metrica('Visitas encontradas', (clone $query)->count()),
            $this->metrica('Visitas realizadas', (clone $query)->where('estado', 'Realizada')->count()),
            $this->metrica('Visitas reprogramadas', (clone $query)->where('estado', 'Reprogramada')->count()),
        ];
        $query->with(['rutaMensajeria.mensajero', 'odontologo.clinica', 'clinica', 'ordenTrabajo.odontologo.clinica'])
            ->orderByDesc('ruta_mensajeria_id')->orderBy('orden_visita')->orderBy('id');
        $columnas = $this->columnas([
            'fecha' => 'Fecha de ruta', 'ruta' => 'Ruta', 'mensajero' => 'Mensajero', 'tipo' => 'Movimiento',
            'doctor' => 'Odontólogo', 'clinica' => 'Clínica', 'orden' => 'Orden', 'estado' => 'Estado de visita',
            'estado_ruta' => 'Estado de ruta', 'hora' => 'Hora realizada', 'recibido' => 'Recibido por', 'direccion' => 'Referencia de dirección',
        ]);
        $mapear = function ($r) {
            $doctor = $r->odontologo ?? $r->ordenTrabajo?->odontologo;
            $clinica = $r->clinica ?? $doctor?->clinica;
            return [
                'fecha' => $r->rutaMensajeria?->fecha?->format('d/m/Y'), 'ruta' => '#'.$r->ruta_mensajeria_id,
                'mensajero' => $r->rutaMensajeria?->mensajero?->name, 'tipo' => $r->tipo_movimiento,
                'doctor' => $doctor?->nombre ?? 'Sin odontólogo', 'clinica' => $clinica?->nombre ?? 'Sin clínica',
                'orden' => $this->codigoOrden($r->ordenTrabajo), 'estado' => $r->estado,
                'estado_ruta' => $r->rutaMensajeria?->estado, 'hora' => $r->hora_realizada,
                'recibido' => $r->recibido_por, 'direccion' => $r->direccion_referencia,
            ];
        };
        $notas = ['', ' '];
        return $this->base('Mensajería', 'Entregas, recolecciones y visitas por fecha de ruta', $filtros, $campos, $query, $columnas, $mapear, $metricas, $notas);
    }

}
