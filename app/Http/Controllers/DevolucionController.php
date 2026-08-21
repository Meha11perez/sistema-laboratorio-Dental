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
            'tipo' => 'required|in:Garantia,Devolucion',

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

            'estado' => 'required|in:Pendiente,En revision,Resuelta',

            'observaciones' => 'nullable|string|max:1000',
        ]);


        DB::transaction(function () use ($datos, $orden) {

            $garantia = null;

            /*
             * Si el usuario indica que se trata de una garantía,
             * buscamos la garantía asociada a la orden.
             */
            if ($datos['tipo'] === 'Garantia') {

                $garantia = $orden->garantia;

                /*
                 * Si todavía no existe garantía, por ahora
                 * podemos crearla automáticamente.
                 */
                if (!$garantia) {

                    $garantia = Garantia::create([
                        'orden_trabajo_id' => $orden->id,
                        'fecha_inicio' => now()->toDateString(),
                        'fecha_vencimiento' => now()
                            ->addMonths(3)
                            ->toDateString(),
                        'estado' => 'Vigente',
                        'observaciones' =>
                            'Garantía creada al registrar devolución.',
                    ]);
                }
            }


            Devolucion::create([
                'orden_trabajo_id' => $orden->id,

                'garantia_id' => $garantia?->id,

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