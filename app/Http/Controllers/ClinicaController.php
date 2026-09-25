<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
public function index(Request $request)
    {
        $query = Clinica::query();


        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA GENERAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'telefono',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'correo',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'direccion',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'nit',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'asistente_secretaria',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'departamento',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'municipio',
                    'like',
                    '%' . $buscar . '%'
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('departamento')) {

            $query->where(
                'departamento',
                $request->departamento
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO MUNICIPIO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('municipio')) {

            $query->where(
                'municipio',
                $request->municipio
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO ESTADO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {

                $query->where(
                    'estado',
                    true
                );
            }

            if ($request->estado === 'inactivo') {

                $query->where(
                    'estado',
                    false
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LISTADO
        |--------------------------------------------------------------------------
        */

        $clinicas = $query
            ->withCount('odontologos')
            ->orderByRaw(
                'departamento IS NULL, departamento ASC'
            )
            ->orderByRaw(
                'municipio IS NULL, municipio ASC'
            )
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA FILTROS
        |--------------------------------------------------------------------------
        */

        $departamentos = Clinica::query()
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento');


        $municipios = Clinica::query()
            ->whereNotNull('municipio')
            ->where('municipio', '!=', '')
            ->distinct()
            ->orderBy('municipio')
            ->pluck('municipio');


        /*
        |--------------------------------------------------------------------------
        | INDICADORES
        |--------------------------------------------------------------------------
        */

        $totalClinicas =
            Clinica::count();

        $clinicasActivas =
            Clinica::where(
                'estado',
                true
            )->count();

        $clinicasInactivas =
            Clinica::where(
                'estado',
                false
            )->count();

        $clinicasConOdontologos =
            Clinica::whereHas(
                'odontologos'
            )->count();


        return view(
            'administracion.clinicas.index',
            compact(
                'clinicas',
                'departamentos',
                'municipios',
                'totalClinicas',
                'clinicasActivas',
                'clinicasInactivas',
                'clinicasConOdontologos'
            )
        );
    }
    
    public function create()
    {
        return view(
            'administracion.clinicas.create'
        );
    }
public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'direccion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nit' => [
                'nullable',
                'string',
                'max:30',
            ],

            'asistente_secretaria' => [
                'nullable',
                'string',
                'max:150',
            ],

            'departamento' => [
                'nullable',
                'string',
                'max:100',
            ],

            'municipio' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);


        Clinica::create([
            'nombre' =>
                $datos['nombre'],

            'telefono' =>
                $datos['telefono'] ?? null,

            'correo' =>
                $datos['correo'] ?? null,

            'direccion' =>
                $datos['direccion'] ?? null,

            'nit' =>
                $datos['nit'] ?? null,

            'asistente_secretaria' =>
                $datos['asistente_secretaria'] ?? null,

            'departamento' =>
                $datos['departamento'] ?? null,

            'municipio' =>
                $datos['municipio'] ?? null,

            'estado' => true,
        ]);


        return redirect()
            ->route('administracion.clinicas.index')
            ->with(
                'success',
                'Clínica registrada correctamente.'
            );
    }
public function edit(Clinica $clinica)
    {
        return view(
            'administracion.clinicas.edit',
            compact('clinica')
        );
}

public function update(Request $request, Clinica $clinica) 
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'direccion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nit' => [
                'nullable',
                'string',
                'max:30',
            ],

            'asistente_secretaria' => [
                'nullable',
                'string',
                'max:150',
            ],

            'departamento' => [
                'nullable',
                'string',
                'max:100',
            ],

            'municipio' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);


        $clinica->update([
            'nombre' =>
                $datos['nombre'],

            'telefono' =>
                $datos['telefono'] ?? null,

            'correo' =>
                $datos['correo'] ?? null,

            'direccion' =>
                $datos['direccion'] ?? null,

            'nit' =>
                $datos['nit'] ?? null,

            'asistente_secretaria' =>
                $datos['asistente_secretaria'] ?? null,

            'departamento' =>
                $datos['departamento'] ?? null,

            'municipio' =>
                $datos['municipio'] ?? null,
        ]);


        return redirect()
            ->route('administracion.clinicas.index')
            ->with(
                'success',
                'Clínica actualizada correctamente.'
            );
    }
public function toggleEstado(Clinica $clinica)
    {
        /*
        |--------------------------------------------------------------------------
        | DESACTIVAR
        |--------------------------------------------------------------------------
        | No permitimos desactivar una clínica mientras
        | tenga odontólogos activos asociados.
        */

        if ($clinica->estado) {

            $tieneOdontologosActivos = $clinica
                ->odontologos()
                ->where('estado', true)
                ->exists();


            if ($tieneOdontologosActivos) {

                return back()->with(
                    'error',
                    'No puede desactivar esta clínica porque tiene odontólogos activos asociados.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR ESTADO
        |--------------------------------------------------------------------------
        */

        $clinica->update([
            'estado' => !$clinica->estado,
        ]);


        return back()->with(
            'success',
            $clinica->estado
                ? 'Clínica activada correctamente.'
                : 'Clínica desactivada correctamente.'
        );
    }
}