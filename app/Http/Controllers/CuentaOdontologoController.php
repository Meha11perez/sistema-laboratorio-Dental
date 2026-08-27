<?php

namespace App\Http\Controllers;

use App\Models\CuentaOdontologo;
use App\Models\Odontologo;
use Illuminate\Http\Request;

class CuentaOdontologoController extends Controller
{
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
}