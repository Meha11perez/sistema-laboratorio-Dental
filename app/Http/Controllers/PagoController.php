<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\OrdenTrabajo;
use App\Models\CuentaOdontologo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function show(OrdenTrabajo $orden)
    {
        /*
         * Buscamos la cuenta del odontólogo.
         * Puede no existir todavía.
         */
        $cuenta = CuentaOdontologo::where(
            'odontologo_id',
            $orden->odontologo_id
        )->first();


        /*
         * Buscamos o creamos el registro principal
         * de pago de la orden.
         */
        $pago = Pago::firstOrCreate(
            [
                'orden_trabajo_id' => $orden->id,
            ],
            [
                'odontologo_id' => $orden->odontologo_id,

                'cuenta_odontologo_id' =>
                    $cuenta?->id,

                'registrado_por' =>
                    auth()->id(),

                'monto_total' =>
                    $orden->total ?? 0,

                'monto_pagado' =>
                    0,

                'saldo_pendiente' =>
                    $orden->total ?? 0,

                'estado_pago' =>
                    ($orden->total ?? 0) > 0
                        ? 'Pendiente'
                        : 'Pagado',

                'fecha_registro' =>
                    now()->toDateString(),

                'fecha_vencimiento' =>
                    $orden->fecha_entrega_estimada,

                'observaciones' =>
                    null,
            ]
        );


        /*
         * Cargamos relaciones necesarias
         * para mostrar el detalle.
         */
        $pago->load([
            'ordenTrabajo.paciente',
            'ordenTrabajo.odontologo',
            'cuentaOdontologo',
            'abonos.usuarioRegistro',
        ]);


        return view('pagos.show', compact(
            'orden',
            'pago'
        ));
    }
}