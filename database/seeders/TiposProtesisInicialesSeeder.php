<?php

namespace Database\Seeders;

use App\Models\OrdenTrabajo;
use App\Models\TipoProtesis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TiposProtesisInicialesSeeder extends Seeder
{
    public function run(): void
    {
        $creados = DB::transaction(function () {
            if (TipoProtesis::exists()) {
                return false;
            }

            if (OrdenTrabajo::exists()) {
                throw new RuntimeException(
                    'Existen órdenes y el catálogo de tipos está vacío. '
                    . 'Revise sus referencias antes de inicializar los tipos.'
                );
            }

            // IDs y nombres usados por ConfigurarAreasProduccionSeeder.
            // Se crean únicamente cuando el catálogo está vacío.
            $tipos = [
                [1, 'Prótesis Fija', 'fija', 'fija'],
                [2, 'PROTESIS REMOVIBLES TERMINADOS', 'removible', 'removible'],
                [3, 'PROTESIS REMOVIBLE PRUEBA DE DIENTES', 'removible', 'removible'],
                [4, 'PRUEBAS DE RODETES Y CUBETAS INDIVIDUALES', 'removible', 'removible'],
                [5, 'CROMOS', 'fija', 'cromo_cobalto'],
                [6, 'Aparato de Ortodoncia', 'ortodoncia', 'ortodoncia'],
            ];

            foreach ($tipos as [$id, $nombre, $categoria, $area]) {
                $tipo = new TipoProtesis();
                $tipo->id = $id;
                $tipo->fill([
                    'nombre' => $nombre,
                    'categoria' => $categoria,
                    'area_trabajo' => $area,
                    'estado' => true,
                ]);
                $tipo->save();
            }

            return true;
        });

        $this->command?->info(
            $creados
                ? 'Se crearon los seis tipos de prótesis iniciales.'
                : 'El catálogo ya contiene tipos. Se conservaron sus datos.'
        );
    }
}
