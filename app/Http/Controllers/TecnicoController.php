<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    public function index(Request $request)
    {
        $query = Tecnico::with('user')
            ->withCount('etapasProduccion');


        // =================================================
        // BUSCAR
        // =================================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'especialidad',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'telefono',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhereHas(
                    'user',
                    function ($usuario) use ($buscar) {

                        $usuario
                            ->where(
                                'name',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'email',
                                'like',
                                '%' . $buscar . '%'
                            );
                    }
                );
            });
        }


        // =================================================
        // ESTADO
        // =================================================
        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {
                $query->where('estado', true);
            }

            if ($request->estado === 'inactivo') {
                $query->where('estado', false);
            }
        }


        // =================================================
        // LISTADO
        // =================================================
        $tecnicos = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();


        // =================================================
        // INDICADORES
        // =================================================
        $totalTecnicos = Tecnico::count();

        $tecnicosActivos = Tecnico::where(
            'estado',
            true
        )->count();

        $tecnicosInactivos = Tecnico::where(
            'estado',
            false
        )->count();

        $tecnicosConEtapas = Tecnico::whereHas(
            'etapasProduccion'
        )->count();


        return view(
            'administracion.tecnicos.index',
            compact(
                'tecnicos',
                'totalTecnicos',
                'tecnicosActivos',
                'tecnicosInactivos',
                'tecnicosConEtapas'
            )
        );
    }
}