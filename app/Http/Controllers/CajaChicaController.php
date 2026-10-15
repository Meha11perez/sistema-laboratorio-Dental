<?php

namespace App\Http\Controllers;

use App\Models\{CajaChicaCorreccion, CajaChicaGasto};
use App\Services\CajaChica;
use App\Services\ExportarCajaChicaExcel;
use App\Support\ImporteCajaChica;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class CajaChicaController extends Controller
{
    private function acceso(Request $request, bool $admin = false): void
    {
        abort_unless($request->user(), 403);
        CajaChica::acceso($request->user(), $admin);
    }

    private function mes(Request $request): string
    {
        $request->validate(['mes' => ['nullable', 'date_format:Y-m']]);
        return $request->filled('mes') ? $request->string('mes')->toString() : now('America/Guatemala')->format('Y-m');
    }

    private function consultaMes(string $mes)
    {
        $inicio = CarbonImmutable::createFromFormat('!Y-m', $mes, 'America/Guatemala');
        return CajaChicaGasto::whereBetween('fecha_gasto', [$inicio->toDateString(), $inicio->endOfMonth()->toDateString()]);
    }

    public function index(Request $request)
    {
        $this->acceso($request);
        $mes = $this->mes($request);
        $request->validate(['estado' => ['nullable', Rule::in(CajaChicaGasto::ESTADOS)], 'buscar' => ['nullable', 'string', 'max:150']]);
        $consulta = $this->consultaMes($mes);
        if ($request->filled('estado')) {
            $consulta->where('estado', $request->input('estado'));
        }
        if ($request->filled('buscar')) {
            $buscar = '%'.$request->input('buscar').'%';
            $consulta->where(fn ($q) => $q->where('concepto', 'like', $buscar)->orWhere('beneficiario', 'like', $buscar)
                ->orWhere('referencia', 'like', $buscar)->orWhere('numero_documento', 'like', $buscar)->orWhere('responsable', 'like', $buscar));
        }
        $gastos = $consulta->with('usuarioRegistro')->orderByDesc('fecha_gasto')->orderByDesc('id')->paginate(15)->withQueryString();
        $resumen = CajaChica::resumen($mes.'-01');
        $solicitudesPendientes = CajaChicaGasto::where('estado', 'Pendiente de autorización')->count()
            + CajaChicaCorreccion::where('estado', 'Pendiente')->count();
        return view('caja_chica.index', compact('gastos', 'mes', 'resumen', 'solicitudesPendientes'));
    }

    public function create(Request $request)
    {
        $this->acceso($request);
        return view('caja_chica.create');
    }

    private function validarGasto(Request $request): array
    {
        $datos = $request->validate([
            'fecha_gasto' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guatemala')->toDateString()],
            'concepto' => ['required', 'string', 'max:255'],
            'beneficiario' => ['nullable', 'string', 'max:150'],
            'responsable' => ['required', 'string', 'max:150'],
            'cantidad' => ['required', 'regex:/^\d{1,6}(?:\.\d{1,3})?$/D', 'numeric', 'min:0.001', 'max:999999.999'],
            'precio_unitario' => ['required', 'regex:/^\d{1,10}(?:\.\d{1,2})?$/D', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'metodo_pago' => ['required', Rule::in(['Efectivo', 'Transferencia', 'Depósito', 'Cheque', 'Otro'])],
            'tipo_comprobante' => ['required', Rule::in(['Factura', 'Recibo', 'Sin comprobante'])],
            'nit' => ['nullable', 'string', 'max:30'],
            'numero_documento' => ['nullable', 'string', 'max:150'],
            'referencia' => ['nullable', 'required_if:metodo_pago,Transferencia,Depósito,Cheque', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'comprobante' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'precio_unitario.regex' => 'Ingrese un precio con punto decimal y hasta dos decimales, por ejemplo 25.50.',
            'precio_unitario.min' => 'El precio debe ser mayor a Q0.00.',
            'cantidad.regex' => 'La cantidad admite hasta tres decimales y punto decimal.',
            'fecha_gasto.before_or_equal' => 'La fecha del gasto no puede ser posterior a hoy.',
            'referencia.required_if' => 'Ingrese el número de transferencia, depósito o cheque.',
            'comprobante.max' => 'El comprobante no puede superar los 5 MB.',
            'comprobante.mimes' => 'El comprobante debe ser PDF, JPG o PNG.',
            'comprobante.extensions' => 'La extensión debe ser PDF, JPG o PNG.',
        ]);
        try {
            ImporteCajaChica::total(ImporteCajaChica::cantidadMilesimas($datos['cantidad']), ImporteCajaChica::centavos($datos['precio_unitario']));
        } catch (InvalidArgumentException $error) {
            throw ValidationException::withMessages(['precio_unitario' => $error->getMessage()]);
        }
        return $datos;
    }

    private function guardarArchivo(Request $request): ?string
    {
        if (!$request->hasFile('comprobante')) {
            return null;
        }
        $ruta = $request->file('comprobante')->store('comprobantes/caja-chica', 'local');
        if (!$ruta) {
            throw ValidationException::withMessages(['comprobante' => 'No se pudo guardar el comprobante. Intente nuevamente.']);
        }
        return $ruta;
    }

    private function eliminarArchivoNuevo(?string $ruta): void
    {
        if ($ruta) {
            try {
                Storage::disk('local')->delete($ruta);
            } catch (Throwable $error) {
                report($error);
            }
        }
    }

    public function store(Request $request)
    {
        $this->acceso($request);
        $datos = array_merge($this->validarGasto($request), $request->validate(['clave_registro' => ['required', 'uuid']]));
        $ruta = null;
        try {
            $ruta = $this->guardarArchivo($request);
            $gasto = CajaChica::registrar($datos, $request->user(), $ruta);
            if (!$gasto->wasRecentlyCreated) {
                $this->eliminarArchivoNuevo($ruta);
            }
        } catch (Throwable $error) {
            $this->eliminarArchivoNuevo($ruta);
            throw $error;
        }
        $mensaje = $gasto->estado === 'Pendiente de autorización'
            ? 'Solicitud guardada. El gasto requiere autorización del Administrador por superar el límite mensual.'
            : 'Gasto registrado correctamente.';
        return redirect()->route('caja-chica.show', $gasto)->with('success', $mensaje);
    }

    public function show(Request $request, CajaChicaGasto $gasto)
    {
        $this->acceso($request);
        $gasto->load(['periodo', 'usuarioRegistro', 'usuarioAutorizacion', 'correcciones.usuarioSolicitud', 'correcciones.usuarioResolucion']);
        $resumen = CajaChica::resumen($gasto->fecha_gasto->toDateString());
        $correccionPendiente = $gasto->correcciones->firstWhere('estado', 'Pendiente');
        return view('caja_chica.show', compact('gasto', 'resumen', 'correccionPendiente'));
    }

    public function solicitudes(Request $request)
    {
        $this->acceso($request);
        $gastos = CajaChicaGasto::where('estado', 'Pendiente de autorización')->with('usuarioRegistro')
            ->orderBy('id')->paginate(15, ['*'], 'gastos_page')->withQueryString();
        $correcciones = CajaChicaCorreccion::where('estado', 'Pendiente')->with(['gasto', 'usuarioSolicitud'])
            ->orderBy('id')->paginate(15, ['*'], 'correcciones_page')->withQueryString();
        return view('caja_chica.solicitudes', compact('gastos', 'correcciones'));
    }

    public function resolver(Request $request, CajaChicaGasto $gasto)
    {
        $this->acceso($request, true);
        $datos = $request->validate(['decision' => ['required', Rule::in(['aprobar', 'rechazar'])], 'motivo' => ['required', 'string', 'max:1000']]);
        CajaChica::resolverExcedente($gasto->id, $datos['decision'], $datos['motivo'], $request->user());
        return redirect()->route('caja-chica.show', $gasto)->with('success', 'Decisión registrada con su responsable y fecha.');
    }

    public function solicitarCorreccion(Request $request, CajaChicaGasto $gasto)
    {
        $this->acceso($request);
        abort_if(in_array($gasto->estado, ['Anulado', 'Rechazado'], true), 422, 'Este gasto ya no admite correcciones.');
        if ($gasto->correcciones()->where('estado', 'Pendiente')->exists()) {
            return redirect()->route('caja-chica.show', $gasto)->with('error', 'Este gasto ya tiene una corrección pendiente.');
        }
        return view('caja_chica.corregir', compact('gasto'));
    }

    public function guardarCorreccion(Request $request, CajaChicaGasto $gasto)
    {
        $this->acceso($request);
        $datos = $request->validate([
            'tipo' => ['required', Rule::in(['Corregir', 'Anular'])], 'clave_registro' => ['required', 'uuid'],
            'version_gasto' => ['required', 'integer', 'min:1'], 'motivo_solicitud' => ['required', 'string', 'max:1000'],
        ]);
        if ($datos['tipo'] === 'Corregir') {
            $datos = array_merge($datos, $this->validarGasto($request));
        }
        $ruta = null;
        try {
            $ruta = $datos['tipo'] === 'Corregir' ? $this->guardarArchivo($request) : null;
            $correccion = CajaChica::solicitarCorreccion($gasto->id, $datos, $request->user(), $ruta);
            if (!$correccion->wasRecentlyCreated) {
                $this->eliminarArchivoNuevo($ruta);
            }
        } catch (Throwable $error) {
            $this->eliminarArchivoNuevo($ruta);
            throw $error;
        }
        return redirect()->route('caja-chica.show', $gasto)->with('success', 'Solicitud guardada. El gasto y sus totales conservan sus valores hasta la aprobación del Administrador.');
    }

    public function resolverCorreccion(Request $request, CajaChicaCorreccion $correccion)
    {
        $this->acceso($request, true);
        $datos = $request->validate(['decision' => ['required', Rule::in(['aprobar', 'rechazar'])], 'motivo' => ['required', 'string', 'max:1000']]);
        CajaChica::resolverCorreccion($correccion->id, $datos['decision'], $datos['motivo'], $request->user());
        return redirect()->route('caja-chica.show', $correccion->caja_chica_gasto_id)->with('success', 'Corrección atendida. El historial conserva los valores anteriores y la decisión.');
    }

    private function descargar(?string $ruta, string $nombre)
    {
        abort_unless($ruta && str_starts_with($ruta, 'comprobantes/caja-chica/') && !str_contains($ruta, '..'), 404);
        abort_unless(Storage::disk('local')->exists($ruta), 404);
        return Storage::disk('local')->download($ruta, $nombre.'.'.pathinfo($ruta, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function comprobante(Request $request, CajaChicaGasto $gasto)
    {
        $this->acceso($request);
        return $this->descargar($gasto->comprobante_path, 'comprobante-caja-chica-'.$gasto->id);
    }

    public function comprobanteCorreccion(Request $request, CajaChicaCorreccion $correccion)
    {
        $this->acceso($request);
        $datos = $request->query('version') === 'anterior' ? $correccion->datos_anteriores : $correccion->datos_propuestos;
        return $this->descargar($datos['comprobante_path'] ?? null, 'comprobante-correccion-'.$correccion->id);
    }

    public function imprimir(Request $request)
    {
        $this->acceso($request);
        $mes = $this->mes($request);
        $gastos = $this->consultaMes($mes)->with('usuarioRegistro')->orderBy('fecha_gasto')->orderBy('id')->get();
        $resumen = CajaChica::resumen($mes.'-01');
        return view('caja_chica.imprimir', compact('gastos', 'mes', 'resumen'));
    }

    public function exportar(Request $request)
    {
        $this->acceso($request);
        $mes = $this->mes($request);
        $resumen = CajaChica::resumen($mes.'-01');
        return response()->streamDownload(function () use ($mes, $resumen) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            $escribir = function (array $fila) use ($salida) {
                $fila = array_map(fn ($v) => is_string($v) && preg_match('/^[\x00-\x20]*[=+@\-]/', $v) ? "'".$v : $v, $fila);
                fputcsv($salida, $fila, ';', '"', '');
            };
            $escribir(['Caja chica', $mes]);
            $escribir(['Registrado y autorizado (Q)', ImporteCajaChica::decimal($resumen['gastado'])]);
            $escribir(['Pendiente de autorización (Q)', ImporteCajaChica::decimal($resumen['pendiente'])]);
            $escribir(['Límite mensual (Q)', ImporteCajaChica::decimal($resumen['limite'])]);
            $escribir([]);
            $escribir(['Gasto', 'Fecha', 'N.º documento', 'Especificaciones', 'Proveedor o beneficiario', 'Cantidad', 'Precio unitario Q', 'Total Q', 'Responsable', 'Estado', 'Método', 'Tipo de documento', 'NIT', 'N.º transferencia, depósito o cheque', 'Registrado por']);
            $this->consultaMes($mes)->with('usuarioRegistro')->orderBy('id')->chunkById(200, function ($gastos) use ($escribir) {
                foreach ($gastos as $gasto) {
                    $escribir([$gasto->id, $gasto->fecha_gasto->format('d/m/Y'), $gasto->numero_documento, $gasto->concepto, $gasto->beneficiario,
                        $gasto->cantidad, $gasto->precio_unitario, $gasto->monto, $gasto->responsable, $gasto->estado, $gasto->metodo_pago, $gasto->tipo_comprobante, $gasto->nit,
                        $gasto->referencia, $gasto->usuarioRegistro?->name]);
                }
            });
            fclose($salida);
        }, 'caja-chica-'.$mes.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }

    public function excel(Request $request)
    {
        $this->acceso($request);
        $mes = $this->mes($request);
        [$gastos, $resumen] = DB::transaction(function () use ($mes) {
            $resumen = CajaChica::resumen($mes.'-01');
            $gastos = $this->consultaMes($mes)->with('usuarioRegistro')->orderBy('fecha_gasto')->orderBy('id')->get();
            return [$gastos, $resumen];
        });
        $contenido = ExportarCajaChicaExcel::generar(resource_path('exports/caja_chica.xlsx'), $gastos, $mes, $resumen);
        return response($contenido, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="caja-chica-'.$mes.'.xlsx"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
