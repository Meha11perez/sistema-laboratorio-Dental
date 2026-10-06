<?php

namespace App\Http\Controllers;

use App\Models\EtapaProduccion;
use App\Models\HistorialProduccion;
use App\Models\Tecnico;
use App\Models\TipoProtesis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReporteTecnicoController extends Controller
{
    private const AREAS = [
        'removible' => 'Removibles',
        'fija' => 'Fijas',
        'cromo_cobalto' => 'Cromo Cobalto',
        'ortodoncia' => 'Ortodoncia',
    ];

    public function index(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->consulta($filtros);
        $areas = self::AREAS;
        $resumen = $this->resumen($query);

        // Los catálogos inactivos también pueden tener participación histórica.
        $tecnicos = Tecnico::with('user')->get()
            ->sortBy(fn ($tecnico) => $tecnico->user?->name ?? '')
            ->values();
        $etapas = EtapaProduccion::orderBy('orden')->get();
        $tiposProtesis = TipoProtesis::orderBy('nombre')->get();
        $porTecnico = $this->agruparPorTecnico($query, $tecnicos);

        $historiales = $this->conRelaciones($query)
            ->orderByDesc('fecha_inicio')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        return view('reportes.tecnicos', compact(
            'filtros', 'areas', 'resumen', 'tecnicos', 'etapas',
            'tiposProtesis', 'porTecnico', 'historiales'
        ));
    }

    public function imprimir(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->consulta($filtros);
        $areas = self::AREAS;
        $resumen = $this->resumen($query);
        $tecnicos = Tecnico::with('user')->get();
        $porTecnico = $this->agruparPorTecnico($query, $tecnicos);
        $descripcionFiltros = $this->descripcionFiltros($filtros);
        $historiales = $this->conRelaciones($query)
            ->orderByDesc('fecha_inicio')->orderByDesc('id')->get();

        return view('reportes.imprimir-tecnicos', compact(
            'filtros', 'areas', 'resumen', 'porTecnico',
            'historiales', 'descripcionFiltros'
        ));
    }

    public function exportar(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $query = $this->conRelaciones($this->consulta($filtros));
        $nombre = 'etapas_por_tecnico_'.$filtros['desde'].'_'.$filtros['hasta'].'.csv';

        return response()->streamDownload(function () use ($query) {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fputcsv($archivo, [
                'Registro de historial', 'Técnico del historial', 'Orden',
                'Área actual de la orden', 'Tipo de prótesis actual', 'Etapa',
                'Inicio', 'Fin', 'Estado registrado', 'Observaciones',
            ], ';', '"', '');

            foreach ($query->lazyById(200) as $historial) {
                $orden = $historial->ordenTrabajo;
                $fila = [
                    $historial->id,
                    $historial->tecnico?->user?->name
                        ?? ($historial->tecnico_id ? 'Técnico #'.$historial->tecnico_id : 'Sin técnico asignado'),
                    $orden ? ($orden->codigo_area ?: $orden->codigo) : 'Orden no disponible',
                    self::AREAS[$orden?->area_trabajo ?? ''] ?? 'Área por revisar',
                    $orden?->tipoProtesis?->nombre,
                    $historial->etapaProduccion?->nombre,
                    $historial->fecha_inicio?->format('d/m/Y H:i'),
                    $historial->fecha_fin?->format('d/m/Y H:i'),
                    $historial->estado,
                    $historial->observaciones,
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
            'area_trabajo' => ['nullable', Rule::in(array_keys(self::AREAS))],
            'tipo_protesis_id' => ['nullable', 'integer', Rule::exists((new TipoProtesis)->getTable(), 'id')],
            'etapa_produccion_id' => ['nullable', 'integer', Rule::exists((new EtapaProduccion)->getTable(), 'id')],
            'tecnico_id' => ['nullable', 'integer', Rule::exists((new Tecnico)->getTable(), 'id')],
        ]);
    }

    private function consulta(array $filtros): Builder
    {
        // El período corresponde al inicio de la etapa, no al ingreso de la orden.
        $query = HistorialProduccion::whereDate('fecha_inicio', '>=', $filtros['desde'])
            ->whereDate('fecha_inicio', '<=', $filtros['hasta']);

        foreach (['tecnico_id', 'etapa_produccion_id'] as $campo) {
            if (!empty($filtros[$campo])) {
                $query->where($campo, $filtros[$campo]);
            }
        }
        if (!empty($filtros['area_trabajo']) || !empty($filtros['tipo_protesis_id'])) {
            $query->whereHas('ordenTrabajo', function ($orden) use ($filtros) {
                foreach (['area_trabajo', 'tipo_protesis_id'] as $campo) {
                    if (!empty($filtros[$campo])) {
                        $orden->where($campo, $filtros[$campo]);
                    }
                }
            });
        }

        return $query;
    }

    private function conRelaciones(Builder $query): Builder
    {
        return $query->with(['tecnico.user', 'etapaProduccion', 'ordenTrabajo.tipoProtesis']);
    }

    private function resumen(Builder $query): array
    {
        return [
            'ordenes' => (clone $query)->distinct()->count('orden_trabajo_id'),
            'registros' => (clone $query)->count(),
            'cerradas' => (clone $query)->whereNotNull('fecha_fin')->count(),
        ];
    }

    private function agruparPorTecnico(Builder $query, $tecnicos)
    {
        $catalogo = $tecnicos->keyBy('id');
        $grupos = (clone $query)->select('tecnico_id')
            ->selectRaw('COUNT(DISTINCT orden_trabajo_id) AS ordenes')
            ->selectRaw('COUNT(*) AS registros')
            ->selectRaw('SUM(CASE WHEN fecha_fin IS NOT NULL THEN 1 ELSE 0 END) AS cerradas')
            ->selectRaw('SUM(CASE WHEN fecha_fin IS NULL THEN 1 ELSE 0 END) AS sin_cierre')
            ->groupBy('tecnico_id')->get();

        foreach ($grupos as $grupo) {
            $nombre = $grupo->tecnico_id
                ? ($catalogo->get($grupo->tecnico_id)?->user?->name ?? 'Técnico #'.$grupo->tecnico_id)
                : 'Sin técnico asignado';
            $grupo->setAttribute('nombre_tecnico', $nombre);
        }

        return $grupos->sortBy('nombre_tecnico')->values();
    }

    private function descripcionFiltros(array $filtros): array
    {
        $descripcion = [];
        if (!empty($filtros['area_trabajo'])) {
            $descripcion[] = 'Área: '.self::AREAS[$filtros['area_trabajo']];
        }
        foreach ([
            'tipo_protesis_id' => [TipoProtesis::class, 'Prótesis'],
            'etapa_produccion_id' => [EtapaProduccion::class, 'Etapa'],
        ] as $campo => [$modelo, $etiqueta]) {
            if (!empty($filtros[$campo])) {
                $descripcion[] = $etiqueta.': '.$modelo::find($filtros[$campo])?->nombre;
            }
        }
        if (!empty($filtros['tecnico_id'])) {
            $tecnico = Tecnico::with('user')->find($filtros['tecnico_id']);
            $descripcion[] = 'Técnico: '.($tecnico?->user?->name ?? 'Técnico #'.$filtros['tecnico_id']);
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
