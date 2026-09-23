<?php

namespace App\Http\Controllers;

use App\Models\CuentaOdontologo;
use App\Models\Odontologo;
use App\Models\Pago;
use Illuminate\Http\Request;

class CuentaOdontologoController extends Controller
{
    public function index(Request $request)
    {
        $odontologos = Odontologo::with([
            'clinica',
            'cuenta',
        ])
        ->orderBy('nombre')
        ->paginate(15);

        return view(
            'cuentas_odontologos.index',
            compact('odontologos')
        );
    }


    public function edit(Odontologo $odontologo)
    {
        $cuenta = CuentaOdontologo::firstOrCreate(
            [
                'odontologo_id' => $odontologo->id,
            ],
            [
                'modalidad_pago' => 'Contado',
                'limite_credito' => 0,
                'saldo_pendiente' => 0,
                'estado' => true,
                'observaciones' => null,
            ]
        );

        return view(
            'cuentas_odontologos.edit',
            compact('odontologo', 'cuenta')
        );
    }


    public function update(
        Request $request,
        Odontologo $odontologo
    ) {
        $datos = $request->validate([
            'modalidad_pago' => [
                'required',
                'in:Contado,Semanal,Crédito',
            ],

            'limite_credito' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'estado' => [
                'required',
                'boolean',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        $cuenta = CuentaOdontologo::firstOrCreate(
            [
                'odontologo_id' => $odontologo->id,
            ]
        );


        $cuenta->update([
            'modalidad_pago' =>
                $datos['modalidad_pago'],

            'limite_credito' =>
                $datos['modalidad_pago'] === 'Crédito'
                    ? ($datos['limite_credito'] ?? 0)
                    : 0,

            'estado' =>
                $datos['estado'],

            'observaciones' =>
                $datos['observaciones'] ?? null,
        ]);


        return redirect()
            ->route('cuentas-odontologos.edit', $odontologo)
            ->with(
                'success',
                'Configuración de pago actualizada correctamente.'
            );
    }
public function show(Odontologo $odontologo)
    {
        // Buscar la cuenta del odontólogo.
        // Si todavía no existe, se crea con valores por defecto.
        $cuenta = CuentaOdontologo::firstOrCreate(
            [
                'odontologo_id' => $odontologo->id,
            ],
            [
                'modalidad_pago' => 'Contado',
                'limite_credito' => 0,
                'saldo_pendiente' => 0,
                'estado' => true,
                'observaciones' => null,
            ]
        );


        // Todas las órdenes financieras del odontólogo.
        $pagos = Pago::with([
            'ordenTrabajo.paciente',
            'abonos',
        ])
            ->where('odontologo_id', $odontologo->id)
            ->latest('fecha_registro')
            ->get();


        // Totales generales del odontólogo.
        $montoTotal = (float) $pagos->sum('monto_total');
        $montoPagado = (float) $pagos->sum('monto_pagado');
        $saldoPendiente = (float) $pagos->sum('saldo_pendiente');


        // Mantener sincronizado el saldo de la cuenta.
        if ((float) $cuenta->saldo_pendiente !== $saldoPendiente) {

            $cuenta->update([
                'saldo_pendiente' => $saldoPendiente,
            ]);

            $cuenta->refresh();
        }


        return view(
            'cuentas_odontologos.show',
            compact(
                'odontologo',
                'cuenta',
                'pagos',
                'montoTotal',
                'montoPagado',
                'saldoPendiente'
            )
        );
    }
}