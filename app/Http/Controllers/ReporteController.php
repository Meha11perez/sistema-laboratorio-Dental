<?php

namespace App\Http\Controllers;

use App\Models\EtapaProduccion;
use App\Models\Odontologo;
use App\Models\Clinica;
use App\Models\EstadoOrden;
use App\Models\OrdenTrabajo;
use App\Models\Tecnico;
use App\Models\TipoProtesis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReporteController extends Controller
{
    private const AREAS = [
        'removible' => 'Removibles',
        'fija' => 'Fijas',
        'cromo_cobalto' => 'Cromo Cobalto',
        'ortodoncia' => 'Ortodoncia',
    ];

    public function index()
    {
        return view('reportes.index');
    }

    public function produccion(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->consultaOrdenes($filtros);
        $resumen = $this->resumen($query);
        $areas = self::AREAS;

        $ordenes = $this->conRelaciones($query)
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Incluye catálogos inactivos para consultar registros históricos.
        $tiposProtesis = TipoProtesis::orderBy('nombre')->get();
        $odontologos = Odontologo::orderBy('nombre')->get();
        $clinicas = Clinica::orderBy('nombre')->get();
        $estados = EstadoOrden::orderBy('nombre')->get();
        $etapas = EtapaProduccion::orderBy('orden')->get();
        $tecnicos = Tecnico::with('user')->get()
            ->sortBy(fn ($tecnico) => $tecnico->user?->name ?? '')
            ->values();

        return view('reportes.produccion', compact(
            'filtros', 'resumen', 'areas', 'ordenes',
            'tiposProtesis', 'estados', 'etapas', 'tecnicos', 'odontologos', 'clinicas'
        ));
    }

    public function trazabilidad(Request $request, OrdenTrabajo $orden)
    {
        $filtros = $this->validarFiltros($request);
        $areas = self::AREAS;
        $orden->load([
            'paciente', 'odontologo', 'tipoProtesis',
            'estadoOrden', 'etapaActual', 'tecnicoActual.user',
        ]);

        // Muestra el historial completo de la orden, sin recortarlo al período.
        $historiales = $orden->historialProduccion()
            ->with(['etapaProduccion', 'tecnico.user', 'usuarioRegistro'])
            ->orderBy('fecha_inicio')
            ->orderBy('id')
            ->get();

        foreach ($historiales as $historial) {
            $minutos = null;
            if ($historial->fecha_inicio && $historial->fecha_fin) {
                $segundos = $historial->fecha_fin->getTimestamp()
                    - $historial->fecha_inicio->getTimestamp();
                if ($segundos >= 0) {
                    $minutos = round($segundos / 60, 2);
                }
            }
            $historial->setAttribute('minutos_transcurridos', $minutos);
        }

        return view('reportes.trazabilidad', compact(
            'orden', 'historiales', 'areas', 'filtros'
        ));
    }

    public function imprimir(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->consultaOrdenes($filtros);
        $resumen = $this->resumen($query);
        $areas = self::AREAS;
        $descripcionFiltros = $this->descripcionFiltros($filtros);

        // La impresión contiene todos los resultados filtrados.
        $ordenes = $this->conRelaciones($query)
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->get();

        return view('reportes.imprimir-produccion', compact(
            'filtros', 'resumen', 'areas', 'ordenes', 'descripcionFiltros'
        ));
    }

    public function exportar(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->conRelaciones($this->consultaOrdenes($filtros));
        $nombre = 'produccion_'.$filtros['desde'].'_'.$filtros['hasta'].'.csv';

        return response()->streamDownload(function () use ($query) {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, [
                'Orden', 'Código de área', 'Ingreso', 'Entrega estimada',
                'Entrega real', 'Área', 'Tipo de prótesis', 'Cantidad solicitada',
                'Paciente', 'Odontólogo', 'Estado actual', 'Etapa actual',
                'Técnico actual', 'Registros de historial',
            ], ';', '"', '');

            foreach ($query->lazyById(200) as $orden) {
                $fila = [
                    $orden->codigo,
                    $orden->codigo_area,
                    $orden->fecha_ingreso?->format('d/m/Y'),
                    $orden->fecha_entrega_estimada?->format('d/m/Y'),
                    $orden->fecha_entrega_real?->format('d/m/Y'),
                    self::AREAS[$orden->area_trabajo] ?? 'Área por revisar',
                    $orden->tipoProtesis?->nombre,
                    $orden->cantidad,
                    trim(($orden->paciente?->nombre ?? '').' '.($orden->paciente?->apellido ?? '')),
                    $orden->odontologo?->nombre,
                    $orden->estadoOrden?->nombre,
                    $orden->etapaActual?->nombre,
                    $orden->tecnicoActual?->user?->name,
                    $orden->historial_produccion_count,
                ];

                fputcsv($archivo, array_map([$this, 'celdaCsv'], $fila), ';', '"', '');
            }
            fclose($archivo);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function validarFiltros(Request $request): array
    {
        $hoy = now('America/Guatemala');
        $request->mergeIfMissing([
            'desde' => $hoy->copy()->subDays(29)->toDateString(),
            'hasta' => $hoy->toDateString(),
        ]);

        return $request->validate([
            'desde' => ['required', 'date_format:Y-m-d'],
            'hasta' => ['required', 'date_format:Y-m-d', 'after_or_equal:desde'],
            'odontologo_id' => ['nullable', 'integer', Rule::exists((new Odontologo)->getTable(), 'id')],
            'clinica_id' => ['nullable', 'integer', Rule::exists((new Clinica)->getTable(), 'id')],
            'area_trabajo' => ['nullable', Rule::in(array_keys(self::AREAS))],
            'tipo_protesis_id' => ['nullable', 'integer', Rule::exists((new TipoProtesis)->getTable(), 'id')],
            'estado_orden_id' => ['nullable', 'integer', Rule::exists((new EstadoOrden)->getTable(), 'id')],
            'etapa_produccion_id' => ['nullable', 'integer', Rule::exists((new EtapaProduccion)->getTable(), 'id')],
            'tecnico_id' => ['nullable', 'integer', Rule::exists((new Tecnico)->getTable(), 'id')],
        ]);
    }

    private function consultaOrdenes(array $filtros): Builder
    {
        $query = OrdenTrabajo::whereDate('fecha_ingreso', '>=', $filtros['desde'])
            ->whereDate('fecha_ingreso', '<=', $filtros['hasta']);

        foreach (['area_trabajo', 'tipo_protesis_id', 'estado_orden_id', 'odontologo_id'] as $campo) {
            if (!empty($filtros[$campo])) {
                $query->where($campo, $filtros[$campo]);
            }
        }

        if (!empty($filtros['clinica_id'])) {
            $query->whereHas('odontologo', fn ($doctor) => $doctor->where('clinica_id', $filtros['clinica_id']));
        }

        // Técnico y etapa deben coincidir en un mismo registro de historial.
        if (!empty($filtros['tecnico_id']) || !empty($filtros['etapa_produccion_id'])) {
            $query->whereHas('historialProduccion', function ($historial) use ($filtros) {
                foreach (['tecnico_id', 'etapa_produccion_id'] as $campo) {
                    if (!empty($filtros[$campo])) {
                        $historial->where($campo, $filtros[$campo]);
                    }
                }
            });
        }

        return $query;
    }

    private function conRelaciones(Builder $query): Builder
    {
        return $query->with([
            'paciente', 'odontologo', 'tipoProtesis',
            'estadoOrden', 'etapaActual', 'tecnicoActual.user',
        ])->withCount('historialProduccion');
    }

    private function resumen(Builder $query): array
    {
        $contarEstado = function (string $nombre) use ($query): int {
            return (clone $query)->whereHas('estadoOrden', function ($estado) use ($nombre) {
                $estado->where('nombre', $nombre);
            })->count();
        };

        return [
            'ordenes' => (clone $query)->count(),
            'cantidad' => (int) (clone $query)->sum('cantidad'),
            'con_historial' => (clone $query)->whereHas('historialProduccion')->count(),
            'en_proceso' => $contarEstado('En proceso'),
            'terminadas' => $contarEstado('Terminado'),
            'entregadas' => $contarEstado('Entregado'),
        ];
    }

    private function descripcionFiltros(array $filtros): array
    {
        $descripcion = [];
        if (!empty($filtros['area_trabajo'])) {
            $descripcion[] = 'Área: '.self::AREAS[$filtros['area_trabajo']];
        }
        foreach ([
            'odontologo_id' => [Odontologo::class, 'Odontólogo'],
            'clinica_id' => [Clinica::class, 'Clínica'],
            'tipo_protesis_id' => [TipoProtesis::class, 'Tipo de prótesis'],
            'estado_orden_id' => [EstadoOrden::class, 'Estado actual'],
            'etapa_produccion_id' => [EtapaProduccion::class, 'Etapa en historial'],
        ] as $campo => [$modelo, $etiqueta]) {
            if (!empty($filtros[$campo])) {
                $descripcion[] = $etiqueta.': '.$modelo::find($filtros[$campo])?->nombre;
            }
        }
        if (!empty($filtros['tecnico_id'])) {
            $tecnico = Tecnico::with('user')->find($filtros['tecnico_id']);
            $descripcion[] = 'Técnico en historial: '.($tecnico?->user?->name ?? 'Sin usuario asociado');
        }
        return $descripcion;
    }

    private function celdaCsv($valor): string
    {
        $texto = (string) $valor;
        if (preg_match('/^\s*[=+\-@]/u', $texto) || preg_match('/^[\t\r\n]/', $texto)) {
            return "'".$texto;
        }
        return $texto;
    }
}
