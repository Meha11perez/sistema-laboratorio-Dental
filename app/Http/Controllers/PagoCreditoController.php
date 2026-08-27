<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\CuentaOdontologo;
use Illuminate\Http\Request;

class PagoCreditoController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // CONTADORES
        // ==========================================
        $pagosPendientes = Pago::where('estado_pago', 'Pendiente')
            ->count();

        $pagosParciales = Pago::where('estado_pago', 'Parcial')
            ->count();

        $pagosCompletos = Pago::where('estado_pago', 'Pagado')
            ->count();

        $saldoPendienteTotal = Pago::sum('saldo_pendiente');


        // ==========================================
        // CONSULTA
        // ==========================================
        $query = Pago::with([
            'ordenTrabajo.paciente',
            'ordenTrabajo.odontologo',
            'cuentaOdontologo',
            'abonos',
        ]);


        // ==========================================
        // FILTRO POR ESTADO
        // ==========================================
        if ($request->filled('estado')) {
            $query->where(
                'estado_pago',
                $request->estado
            );
        }


        // ==========================================
        // FILTRO POR ODONTÓLOGO
        // ==========================================
        if ($request->filled('odontologo')) {
            $query->whereHas(
                'odontologo',
                function ($q) use ($request) {

                    $q->where(
                        'nombre',
                        'like',
                        '%' . $request->odontologo . '%'
                    );
                }
            );
        }


        // ==========================================
        // FILTRO POR ORDEN
        // ==========================================
        if ($request->filled('orden')) {
            $query->whereHas(
                'ordenTrabajo',
                function ($q) use ($request) {

                    $q->where(
                        'codigo',
                        'like',
                        '%' . $request->orden . '%'
                    );
                }
            );
        }


        $pagos = $query
            ->latest('fecha_registro')
            ->paginate(10)
            ->withQueryString();


        return view('pagos.index', compact(
            'pagosPendientes',
            'pagosParciales',
            'pagosCompletos',
            'saldoPendienteTotal',
            'pagos'
        ));
    }
}