<?php

namespace App\Http\Controllers;

use App\Models\CajaChicaGasto;
use App\Services\CajaChica;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteGastoController extends Controller
{
    public function index(Request $request)
    {
        $datos = $this->datos($request);
        $registros = (clone $datos['consulta'])->paginate(15)->withQueryString();
        unset($datos['consulta']);
        return view('reportes.gastos.index', $datos + ['registros' => $registros, 'imprimir' => false]);
    }

    public function imprimir(Request $request)
    {
        $datos = $this->datos($request);
        $registros = (clone $datos['consulta'])->get();
        unset($datos['consulta']);
        return view('reportes.gastos.imprimir', $datos + ['registros' => $registros, 'imprimir' => true]);
    }

    private function datos(Request $request): array
    {
        abort_unless($request->user(), 403);
        CajaChica::acceso($request->user());
        $validado = $request->validate(['mes' => ['nullable', 'date_format:Y-m']]);
        $mes = ($validado['mes'] ?? '') ?: now('America/Guatemala')->format('Y-m');
        $inicio = CarbonImmutable::createFromFormat('!Y-m', $mes, 'America/Guatemala');
        $fechas = [$inicio->toDateString(), $inicio->addMonth()->toDateString()];
        $mesTexto = $inicio->locale('es')->isoFormat('MMMM [de] YYYY');

        $caja = DB::table('caja_chica_gastos as g')->where('g.fecha_gasto', '>=', $fechas[0])->where('g.fecha_gasto', '<', $fechas[1])
            ->whereIn('g.estado', CajaChicaGasto::CONTABILIZADOS)
            ->selectRaw("g.fecha_gasto AS fecha, 'Caja chica' AS origen, g.id AS registro_id,
                g.concepto, g.beneficiario, g.numero_documento AS documento, '' AS serie,
                g.nit, g.metodo_pago, g.monto_centavos, g.registrado_por");

        // Cada factura aporta un subtotal por tipo. No se suma otra vez su total de cabecera.
        $compras = DB::table('compra_detalles as d')->join('compras as c', 'c.id', '=', 'd.compra_id')
            ->where('c.fecha', '>=', $fechas[0])->where('c.fecha', '<', $fechas[1])->where('c.estado', 'Registrada')
            ->whereIn('d.tipo', ['Material', 'Servicio'])
            ->selectRaw("c.fecha, CASE WHEN d.tipo = 'Material' THEN 'Materiales' ELSE 'Servicios' END AS origen,
                c.id AS registro_id, CASE WHEN d.tipo = 'Material' THEN 'Materiales según factura' ELSE 'Servicios según factura' END AS concepto,
                c.proveedor AS beneficiario, c.numero_factura AS documento, c.serie, c.nit, c.metodo_pago,
                SUM(d.total_centavos) AS monto_centavos, c.registrado_por")
            ->groupBy('c.id', 'c.fecha', 'd.tipo', 'c.proveedor', 'c.numero_factura', 'c.serie', 'c.nit', 'c.metodo_pago', 'c.registrado_por');

        $union = $caja->unionAll($compras);
        $sumas = DB::query()->fromSub(clone $union, 'r')->selectRaw("COUNT(*) AS cantidad,
            COALESCE(SUM(CASE WHEN r.origen = 'Caja chica' THEN r.monto_centavos ELSE 0 END), 0) AS caja,
            COALESCE(SUM(CASE WHEN r.origen = 'Materiales' THEN r.monto_centavos ELSE 0 END), 0) AS materiales,
            COALESCE(SUM(CASE WHEN r.origen = 'Servicios' THEN r.monto_centavos ELSE 0 END), 0) AS servicios,
            COALESCE(SUM(r.monto_centavos), 0) AS total")->first();
        $resumen = array_map('intval', (array) $sumas);
        $consulta = DB::query()->fromSub($union, 'r')->leftJoin('users as u', 'u.id', '=', 'r.registrado_por')
            ->select('r.*', 'u.name as usuario')->orderByDesc('r.fecha')->orderBy('r.origen')->orderByDesc('r.registro_id');

        $pendientesCaja = DB::table('caja_chica_gastos')->where('fecha_gasto', '>=', $fechas[0])->where('fecha_gasto', '<', $fechas[1])
            ->where('estado', 'Pendiente de autorización')->count();

        return compact('mes', 'mesTexto', 'resumen', 'consulta', 'pendientesCaja');
    }
}
