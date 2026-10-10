<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AbonoController extends Controller
{
    public function store(Request $request, Pago $pago)
    {
        $datos = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo_pago' => ['required', 'in:Efectivo,Transferencia,Depósito,Otro'],
            'fecha_abono' => ['required', 'date'],
            'referencia' => ['nullable', 'required_if:metodo_pago,Transferencia,Depósito', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string'],
            'comprobante' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'monto.required' => 'Debe ingresar el monto del abono.',
            'monto.numeric' => 'El monto del abono debe ser un valor numérico.',
            'monto.min' => 'El monto del abono debe ser mayor a Q0.00.',
            'metodo_pago.required' => 'Debe seleccionar un método de pago.',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido.',
            'fecha_abono.required' => 'Debe seleccionar la fecha del abono.',
            'fecha_abono.date' => 'La fecha del abono no es válida.',
            'referencia.required_if' => 'Debe ingresar el número de referencia o boleta para pagos por transferencia o depósito.',
            'referencia.max' => 'La referencia no puede contener más de 100 caracteres.',
            'observaciones.string' => 'Las observaciones deben contener texto.',
            'comprobante.file' => 'No se pudo cargar el comprobante.',
            'comprobante.uploaded' => 'No se pudo cargar el comprobante. Revise el tamaño del archivo.',
            'comprobante.mimes' => 'El comprobante debe ser PDF, JPG o PNG.',
            'comprobante.extensions' => 'La extensión del comprobante debe ser PDF, JPG o PNG.',
            'comprobante.max' => 'El comprobante no puede superar los 5 MB.',
        ]);

        $rutaComprobante = null;

        try {
            if ($request->hasFile('comprobante')) {
                $rutaComprobante = $request->file('comprobante')
                    ->store('comprobantes/abonos', 'local');

                if (!$rutaComprobante) {
                    throw ValidationException::withMessages([
                        'comprobante' => 'No se pudo guardar el comprobante. Intente nuevamente.',
                    ]);
                }
            }

            DB::transaction(function () use ($datos, $pago, $rutaComprobante) {
                // Conservamos el bloqueo para comprobar el saldo actualizado.
                $pago = Pago::whereKey($pago->id)->lockForUpdate()->firstOrFail();

                if ((float) $pago->saldo_pendiente <= 0) {
                    throw ValidationException::withMessages([
                        'monto' => 'Este pago ya se encuentra completamente pagado.',
                    ]);
                }

                if ((float) $datos['monto'] > (float) $pago->saldo_pendiente) {
                    throw ValidationException::withMessages([
                        'monto' => 'El abono no puede ser mayor al saldo pendiente.',
                    ]);
                }

                Abono::create([
                    'pago_id' => $pago->id,
                    'registrado_por' => auth()->id(),
                    'monto' => $datos['monto'],
                    'metodo_pago' => $datos['metodo_pago'],
                    'fecha_abono' => $datos['fecha_abono'],
                    'referencia' => $datos['referencia'] ?? null,
                    'observaciones' => $datos['observaciones'] ?? null,
                    'comprobante_path' => $rutaComprobante,
                ]);

                // Conservamos el cálculo a partir de todos los abonos.
                $totalAbonado = (float) $pago->abonos()->sum('monto');
                $montoTotal = (float) $pago->monto_total;
                $saldoPendiente = max(0, $montoTotal - $totalAbonado);

                if ($saldoPendiente <= 0) {
                    $estadoPago = 'Pagado';
                } elseif ($totalAbonado > 0) {
                    $estadoPago = 'Parcial';
                } else {
                    $estadoPago = 'Pendiente';
                }

                $pago->update([
                    'monto_pagado' => $totalAbonado,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado_pago' => $estadoPago,
                ]);

                if ($pago->cuentaOdontologo) {
                    $cuenta = $pago->cuentaOdontologo;
                    $saldoCuenta = (float) $cuenta->pagos()->sum('saldo_pendiente');

                    $cuenta->update([
                        'saldo_pendiente' => $saldoCuenta,
                    ]);
                }
            });
        } catch (Throwable $error) {
            // Si el abono falla, eliminamos el archivo recién cargado.
            if ($rutaComprobante) {
                try {
                    Storage::disk('local')->delete($rutaComprobante);
                } catch (Throwable $errorArchivo) {
                    report($errorArchivo);
                }
            }

            throw $error;
        }

        return back()->with('success', 'Abono registrado correctamente.');
    }

    public function comprobante(Abono $abono)
    {
        $ruta = $abono->comprobante_path;

        abort_unless($ruta && str_starts_with($ruta, 'comprobantes/abonos/'), 404);
        abort_unless(Storage::disk('local')->exists($ruta), 404);

        $nombre = 'comprobante-abono-' . $abono->id . '.' . pathinfo($ruta, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($ruta, $nombre, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}