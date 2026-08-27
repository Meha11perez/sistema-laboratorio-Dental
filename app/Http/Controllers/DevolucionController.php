<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Garantia;
use App\Models\OrdenTrabajo;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function create(OrdenTrabajo $orden)
    {
        $orden->load([
            'paciente',
            'odontologo',
            'tipoProtesis',
            'garantia',
        ]);

        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->get();

        return view('devoluciones.create', compact(
            'orden',
            'tecnicos'
        ));
    }


    public function store(Request $request, OrdenTrabajo $orden)
    {
        $datos = $request->validate([
            'tipo' => 'required|in:Devolución,Repetición,Corrección',

            'motivo' => 'required|string|max:255',

            'fecha_devolucion' => 'required|date',

            'tecnico_responsable_id' => [
                'nullable',
                'exists:tecnicos,id',
            ],

            'requiere_repeticion' => 'required|boolean',

            'perdida_estimada' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'estado' => 'required|in:Registrada,En revisión,En corrección,Resuelta,Rechazada',

            'observaciones' => 'nullable|string|max:1000',
        ]);
                DB::transaction(function () use ($datos, $orden) {

                    // Buscar la garantía asociada a la orden
                    $garantia = $orden->garantia;

                    $garantiaId = null;

                    // Si existe garantía y todavía está vigente,
                    // la asociamos automáticamente con la devolución
                    if (
                        $garantia &&
                        $garantia->fecha_vencimiento &&
                        now()->startOfDay()->lte(
                            $garantia->fecha_vencimiento->copy()->startOfDay()
                        )
                    ) {
                        $garantiaId = $garantia->id;
                    }


                    Devolucion::create([
                        'orden_trabajo_id' => $orden->id,

                        'garantia_id' => $garantiaId,

                        'tecnico_responsable_id' =>
                            $datos['tecnico_responsable_id'] ?? null,

                        'registrado_por' => auth()->id(),

                        'tipo' => $datos['tipo'],

                        'motivo' => $datos['motivo'],

                        'fecha_devolucion' =>
                            $datos['fecha_devolucion'],

                        'requiere_repeticion' =>
                            $datos['requiere_repeticion'],

                        'perdida_estimada' =>
                            $datos['perdida_estimada'] ?? 0,

                        'estado' => $datos['estado'],

                        'observaciones' =>
                            $datos['observaciones'] ?? null,
                    ]);
                });

                return redirect()
                        ->route('ordenes.show', $orden)
                        ->with(
                            'success',
                            'Devolución registrada correctamente.'
                        );
                }
}