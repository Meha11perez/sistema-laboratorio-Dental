<?php

namespace App\Http\Controllers;

use App\Models\{Compra, CompraCorreccion, Material};
use App\Services\RegistroCompra;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class CompraController extends Controller
{
    private function acceso(Request $request, bool $admin = false): void
    {
        abort_unless($request->user(), 403);
        RegistroCompra::acceso($request->user(), $admin);
    }

    public function index(Request $request)
    {
        $this->acceso($request);
        $request->validate(['mes' => ['nullable', 'date_format:Y-m'], 'buscar' => ['nullable', 'string', 'max:150'],
            'estado' => ['nullable', 'in:Registrada,Anulada'], 'recepcion' => ['nullable', 'in:pendiente,recibida,servicios']]);
        $mes = $request->input('mes') ?: now('America/Guatemala')->format('Y-m');
        $inicio = CarbonImmutable::createFromFormat('!Y-m', $mes, 'America/Guatemala');
        $base = Compra::whereBetween('fecha', [$inicio->toDateString(), $inicio->endOfMonth()->toDateString()]);
        $totalMes = (int) (clone $base)->where('estado', 'Registrada')->sum('total_centavos');
        $pendientes = (clone $base)->where('estado', 'Registrada')->whereNull('fecha_recepcion')->whereHas('detalles', fn ($q) => $q->where('tipo', 'Material'))->count();
        if ($request->filled('buscar')) {
            $texto = '%'.$request->input('buscar').'%';
            $base->where(fn ($q) => $q->where('proveedor', 'like', $texto)->orWhere('nit', 'like', $texto)->orWhere('numero_factura', 'like', $texto));
        }
        if ($request->filled('estado')) $base->where('estado', $request->input('estado'));
        if ($request->input('recepcion') === 'recibida') $base->whereNotNull('fecha_recepcion');
        if ($request->input('recepcion') === 'servicios') $base->whereDoesntHave('detalles', fn ($q) => $q->where('tipo', 'Material'));
        if ($request->input('recepcion') === 'pendiente') $base->where('estado', 'Registrada')->whereNull('fecha_recepcion')->whereHas('detalles', fn ($q) => $q->where('tipo', 'Material'));
        $compras = $base->with(['registrador', 'detalles'])->orderByDesc('fecha')->orderByDesc('id')->paginate(15)->withQueryString();
        $solicitudes = CompraCorreccion::where('estado', 'Pendiente')->count();
        return view('compras.index', compact('compras', 'mes', 'totalMes', 'pendientes', 'solicitudes'));
    }

    public function create(Request $request)
    {
        $this->acceso($request);
        $materiales = Material::where('estado', true)->orderBy('nombre')->get();
        return view('compras.create', compact('materiales'));
    }

    private function validarArchivo(Request $request, bool $obligatorio): void
    {
        $request->validate(['factura' => [$obligatorio ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png',
            'extensions:pdf,jpg,jpeg,png', 'max:5120']], ['factura.required' => 'Adjunte la factura en PDF, JPG o PNG.', 'factura.max' => 'La factura admite hasta 5 MB.']);
    }

    private function guardarArchivo(Request $request): ?string
    {
        if (!$request->hasFile('factura')) return null;
        $ruta = $request->file('factura')->store('comprobantes/compras', 'local');
        if (!$ruta) throw ValidationException::withMessages(['factura' => 'No se pudo guardar la factura.']);
        return $ruta;
    }

    private function limpiar(?string $ruta): void
    {
        if ($ruta) {
            try { Storage::disk('local')->delete($ruta); } catch (Throwable $e) { report($e); }
        }
    }

    public function store(Request $request)
    {
        $this->acceso($request);
        $datos = RegistroCompra::validar($request->all());
        $datos += $request->validate(['clave_registro' => ['required', 'uuid']]);
        $this->validarArchivo($request, true);
        $ruta = null;
        try {
            $ruta = $this->guardarArchivo($request);
            $compra = RegistroCompra::registrar($datos, $request->user(), $ruta);
            if (!$compra->wasRecentlyCreated) $this->limpiar($ruta);
        } catch (Throwable $e) { $this->limpiar($ruta); throw $e; }
        return redirect()->route('compras.show', $compra)->with('success', 'Compra registrada. Los materiales quedan pendientes de recepción por el Administrador. Los servicios no generan inventario.');
    }

    public function show(Request $request, Compra $compra)
    {
        $this->acceso($request);
        $compra->load(['detalles.material', 'detalles.movimiento', 'registrador', 'receptor', 'correcciones.solicitante', 'correcciones.resolutor', 'historiales.usuario']);
        $pendiente = $compra->correcciones->firstWhere('estado', 'Pendiente');
        return view('compras.show', compact('compra', 'pendiente'));
    }

    public function solicitudes(Request $request)
    {
        $this->acceso($request);
        $solicitudes = CompraCorreccion::with(['compra', 'solicitante'])->orderByRaw("CASE WHEN estado = 'Pendiente' THEN 0 ELSE 1 END")
            ->orderByDesc('id')->paginate(15)->withQueryString();
        return view('compras.solicitudes', compact('solicitudes'));
    }

    public function corregir(Request $request, Compra $compra)
    {
        $this->acceso($request);
        abort_if($compra->estado === 'Anulada', 403, 'Esta compra está anulada.');
        $compra->load('detalles');
        $ids = $compra->detalles->pluck('material_id')->filter();
        $materiales = Material::where(fn ($q) => $q->where('estado', true)->orWhereIn('id', $ids))->orderBy('nombre')->get();
        return view('compras.corregir', compact('compra', 'materiales'));
    }

    public function solicitar(Request $request, Compra $compra)
    {
        $this->acceso($request);
        $datos = $request->validate(['tipo' => ['required', 'in:Corregir,Anular'], 'clave_registro' => ['required', 'uuid'],
            'version_compra' => ['required', 'integer', 'min:1'], 'motivo' => ['required', 'string', 'min:5', 'max:1000']]);
        if ($datos['tipo'] === 'Corregir') $datos += RegistroCompra::validar($request->all());
        $this->validarArchivo($request, false);
        $ruta = null;
        try {
            if ($datos['tipo'] === 'Corregir') $ruta = $this->guardarArchivo($request);
            $solicitud = RegistroCompra::solicitar($compra->id, $datos, $request->user(), $ruta);
            if (!$solicitud->wasRecentlyCreated) $this->limpiar($ruta);
        } catch (Throwable $e) { $this->limpiar($ruta); throw $e; }
        return redirect()->route('compras.show', $compra)->with('success', 'Solicitud guardada. Los datos originales se conservan hasta que el Administrador la apruebe.');
    }

    public function resolver(Request $request, CompraCorreccion $correccion)
    {
        $this->acceso($request, true);
        $datos = $request->validate(['decision' => ['required', 'in:aprobar,rechazar'], 'motivo' => ['required', 'string', 'min:5', 'max:1000']]);
        RegistroCompra::resolver($correccion->id, $datos['decision'], $datos['motivo'], $request->user());
        return redirect()->route('compras.show', $correccion->compra_id)->with('success', 'La decisión quedó registrada en el historial.');
    }

    public function recibir(Request $request, Compra $compra)
    {
        $this->acceso($request, true);
        $datos = $request->validate(['version_compra' => ['required', 'integer', 'min:1'], 'motivo' => ['required', 'string', 'min:5', 'max:1000'], 'confirmar' => ['accepted']]);
        RegistroCompra::recibir($compra->id, (int) $datos['version_compra'], $datos['motivo'], $request->user());
        return redirect()->route('compras.show', $compra)->with('success', 'Recepción confirmada. Cada material tiene una única entrada vinculada.');
    }

    public function comprobante(Request $request, Compra $compra)
    {
        $this->acceso($request);
        $request->validate(['correccion' => ['nullable', 'integer', 'min:1'], 'version' => ['nullable', 'in:anterior,propuesta']]);
        $ruta = $compra->factura_path;
        if ($request->filled('correccion')) {
            $correccion = $compra->correcciones()->findOrFail($request->input('correccion'));
            $datos = $request->input('version') === 'propuesta' ? $correccion->propuestos : $correccion->anteriores;
            $ruta = $datos['factura_path'] ?? null;
        }
        abort_unless($ruta && str_starts_with($ruta, 'comprobantes/compras/') && Storage::disk('local')->exists($ruta), 404);
        $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
        return Storage::disk('local')->download($ruta, 'factura-compra-'.$compra->id.'.'.$extension, [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
