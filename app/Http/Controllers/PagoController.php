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

                    'estado_pago' => 'Pendiente',
                    
                    'fecha_registro' =>
                        now()->toDateString(),

                    'fecha_vencimiento' =>
                        $orden->fecha_entrega_estimada,

                    'observaciones' =>
                        null,
                ]
            );

            /*
            * ==========================================
            * SINCRONIZAR TOTAL DE LA ORDEN
            * ==========================================
            *
            * Si el total de la orden fue modificado,
            * actualizamos el pago sin perder los abonos.
            */
            $montoOrden = (float) ($orden->total ?? 0);

            $totalAbonado = (float) $pago
                ->abonos()
                ->sum('monto');

            $saldoPendiente = max(
                0,
                $montoOrden - $totalAbonado
            );
            /*
            * Determinamos nuevamente el estado del pago.
            */
          if ($montoOrden <= 0) {

            // Todavía no se ha definido el precio.
            $estadoPago = 'Pendiente';

        } elseif ($saldoPendiente <= 0) {

            $estadoPago = 'Pagado';

        } elseif ($totalAbonado > 0) {

            $estadoPago = 'Parcial';

        } else {

            $estadoPago = 'Pendiente';
        }
        
        $pago->update([
            'monto_total' => $montoOrden,

            'monto_pagado' => $totalAbonado,

            'saldo_pendiente' => $saldoPendiente,

            'estado_pago' => $estadoPago,
        ]);

        /*
            * Si la cuenta fue creada después del pago,
            * la asociamos.
            */
        if ($cuenta && !$pago->cuenta_odontologo_id) {

            $pago->update([
                'cuenta_odontologo_id' => $cuenta->id,
            ]);
        }

        /*
            * Sincronizamos el saldo general de la cuenta
            * utilizando todos los pagos asociados.
            */
        if ($cuenta) {

            $saldoCuenta = Pago::where(
                    'cuenta_odontologo_id',
                    $cuenta->id
                )
                ->sum('saldo_pendiente');

            $cuenta->update([
                'saldo_pendiente' => $saldoCuenta,
            ]);
        }

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
    public function actualizarMonto(Request $request, Pago $pago)
    {
        // ==========================================
        // SEGURIDAD
        // ==========================================
        $rol = auth()->user()?->role?->nombre;

        if (!in_array($rol, ['Administrador', 'Recepcion'], true)) {
            abort(
                403,
                'No tiene permiso para modificar el monto de una orden.'
            );
        }
        // ==========================================
        // VALIDAR MONTO
        // ==========================================
        $datos = $request->validate([
            'monto_total' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);


        DB::transaction(function () use ($datos, $pago) {

            // Bloquear el registro mientras se modifica.
            $pago = Pago::with([
                'ordenTrabajo',
                'cuentaOdontologo',
            ])
                ->whereKey($pago->id)
                ->lockForUpdate()
                ->firstOrFail();


            $nuevoTotal = (float) $datos['monto_total'];

            $totalAbonado = (float) $pago
                ->abonos()
                ->sum('monto');


            // ==========================================
            // NO PERMITIR TOTAL MENOR A LO YA PAGADO
            // ==========================================
            if ($nuevoTotal < $totalAbonado) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'monto_total' =>
                        'El monto total no puede ser menor a lo ya pagado: Q '
                        . number_format($totalAbonado, 2)
                        . '.',
                ]);
            }


            // ==========================================
            // RECALCULAR SALDO
            // ==========================================
            $saldoPendiente = max(
                0,
                $nuevoTotal - $totalAbonado
            );


            // ==========================================
            // DETERMINAR ESTADO
            // ==========================================
            if ($saldoPendiente <= 0) {

                $estadoPago = 'Pagado';

            } elseif ($totalAbonado > 0) {

                $estadoPago = 'Parcial';

            } else {

                $estadoPago = 'Pendiente';
            }


            // ==========================================
            // ACTUALIZAR PAGO
            // ==========================================
            $pago->update([
                'monto_total' => $nuevoTotal,
                'monto_pagado' => $totalAbonado,
                'saldo_pendiente' => $saldoPendiente,
                'estado_pago' => $estadoPago,
            ]);


            // ==========================================
            // SINCRONIZAR CON LA ORDEN
            // ==========================================
            if ($pago->ordenTrabajo) {

                $pago->ordenTrabajo->update([
                    'total' => $nuevoTotal,
                ]);
            }


            // ==========================================
            // ACTUALIZAR CUENTA DEL ODONTÓLOGO
            // ==========================================
            if ($pago->cuentaOdontologo) {

                $cuenta = $pago->cuentaOdontologo;

                $saldoCuenta = (float) $cuenta
                    ->pagos()
                    ->sum('saldo_pendiente');

                $cuenta->update([
                    'saldo_pendiente' => $saldoCuenta,
                ]);
            }
        });


        return back()->with(
            'success',
            'Monto del trabajo actualizado correctamente.'
        );
    }
}