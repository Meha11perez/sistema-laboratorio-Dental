<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AbonoController extends Controller
{
    public function store(Request $request, Pago $pago)
    {
        // ==========================================
        // VALIDAR DATOS
        // ==========================================
        $datos = $request->validate([
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'metodo_pago' => [
                'required',
                'in:Efectivo,Transferencia,Depósito,Otro',
            ],

            'fecha_abono' => [
                'required',
                'date',
            ],

            'referencia' => [
                'nullable',
                'string',
                'max:150',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);


        DB::transaction(function () use ($datos, $pago) {

            /*
             * Volvemos a obtener el pago bloqueando el registro.
             * Esto evita problemas si dos usuarios intentan
             * registrar un abono al mismo tiempo.
             */
            $pago = Pago::whereKey($pago->id)
                ->lockForUpdate()
                ->firstOrFail();


            // ==========================================
            // COMPROBAR SALDO
            // ==========================================
            if ((float) $pago->saldo_pendiente <= 0) {

                throw ValidationException::withMessages([
                    'monto' =>
                        'Este pago ya se encuentra completamente pagado.',
                ]);
            }


            if (
                (float) $datos['monto'] >
                (float) $pago->saldo_pendiente
            ) {

                throw ValidationException::withMessages([
                    'monto' =>
                        'El abono no puede ser mayor al saldo pendiente.',
                ]);
            }


            // ==========================================
            // REGISTRAR ABONO
            // ==========================================
            Abono::create([
                'pago_id' => $pago->id,

                'registrado_por' =>
                    auth()->id(),

                'monto' =>
                    $datos['monto'],

                'metodo_pago' =>
                    $datos['metodo_pago'],

                'fecha_abono' =>
                    $datos['fecha_abono'],

                'referencia' =>
                    $datos['referencia'] ?? null,

                'observaciones' =>
                    $datos['observaciones'] ?? null,
            ]);


            // ==========================================
            // RECALCULAR PAGO
            // ==========================================
            $totalAbonado = (float) $pago
                ->abonos()
                ->sum('monto');

            $montoTotal = (float) $pago->monto_total;

            $saldoPendiente = max(
                0,
                $montoTotal - $totalAbonado
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
                'monto_pagado' =>
                    $totalAbonado,

                'saldo_pendiente' =>
                    $saldoPendiente,

                'estado_pago' =>
                    $estadoPago,
            ]);


            // ==========================================
            // ACTUALIZAR CUENTA DEL ODONTÓLOGO
            // ==========================================
            if ($pago->cuentaOdontologo) {

                $cuenta = $pago->cuentaOdontologo;

                /*
                 * Calculamos el saldo general utilizando
                 * todos los pagos de esta cuenta.
                 */
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
            'Abono registrado correctamente.'
        );
    }
}