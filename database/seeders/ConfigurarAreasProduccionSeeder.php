<?php

namespace Database\Seeders;

use App\Models\EtapaProduccion;
use App\Models\OrdenTrabajo;
use App\Models\TipoProtesis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ConfigurarAreasProduccionSeeder extends Seeder
{
    public function run(): void
    {
        // Correspondencias confirmadas con los registros obtenidos mediante Tinker.
        $tipos = [
            1 => ['nombre' => 'Prótesis Fija', 'area' => 'fija'],
            2 => ['nombre' => 'PROTESIS REMOVIBLES TERMINADOS', 'area' => 'removible'],
            3 => ['nombre' => 'PROTESIS REMOVIBLE PRUEBA DE DIENTES', 'area' => 'removible'],
            4 => ['nombre' => 'PRUEBAS DE RODETES Y CUBETAS INDIVIDUALES', 'area' => 'removible'],
            5 => ['nombre' => 'CROMOS', 'area' => 'cromo_cobalto'],
            6 => ['nombre' => 'Aparato de Ortodoncia', 'area' => 'ortodoncia'],
        ];

        $etapas = [
            1 => ['nombre' => 'Cromos', 'areas' => ['cromo_cobalto'], 'final' => true],
            2 => ['nombre' => 'Rodetes y cubetas individuales', 'areas' => ['removible'], 'final' => false],
            3 => ['nombre' => 'Prueba de dientes', 'areas' => ['removible'], 'final' => false],
            4 => ['nombre' => 'Prueba de metal', 'areas' => ['fija'], 'final' => false],
            5 => ['nombre' => 'Biscochos', 'areas' => ['fija'], 'final' => false],
            6 => ['nombre' => 'Terminados', 'areas' => ['removible', 'fija'], 'final' => true],
            7 => ['nombre' => 'Ortodoncia', 'areas' => ['ortodoncia'], 'final' => true],
        ];

        DB::transaction(function () use ($tipos, $etapas) {
            $tiposActuales = TipoProtesis::whereIn('id', array_keys($tipos))
                ->lockForUpdate()->get()->keyBy('id');
            $etapasActuales = EtapaProduccion::whereIn('id', array_keys($etapas))
                ->lockForUpdate()->get()->keyBy('id');

            // Comprobar todos los IDs y nombres antes de modificar el catálogo.
            foreach ($tipos as $id => $configuracion) {
                $this->comprobarNombre($tiposActuales->get($id), $configuracion['nombre'], 'tipo', $id);
            }
            foreach ($etapas as $id => $configuracion) {
                $this->comprobarNombre($etapasActuales->get($id), $configuracion['nombre'], 'etapa', $id);
            }

            foreach ($tipos as $id => $configuracion) {
                $datos = ['area_trabajo' => $configuracion['area']];
                if ($id === 1) {
                    // Corrige Prótesis Fija sin cambiar su ID ni las órdenes asociadas.
                    $datos['categoria'] = 'fija';
                }
                // CROMOS conserva su categoría antigua por compatibilidad con el esquema.
                // La clasificación de producción utiliza ahora area_trabajo.
                $tiposActuales->get($id)->update($datos);
            }

            foreach ($etapas as $id => $configuracion) {
                $etapasActuales->get($id)->update([
                    'areas_trabajo' => $configuracion['areas'],
                    'es_final' => $configuracion['final'],
                ]);
            }
        });

        $this->command?->info('Áreas configuradas. Se conservaron los IDs, nombres e historiales.');
        $this->informarOrdenesIncompatibles();
    }

    private function comprobarNombre($registro, string $esperado, string $clase, int $id): void
    {
        $normalizar = static fn ($nombre) => Str::lower(Str::ascii(trim((string) $nombre)));

        if (!$registro || $normalizar($registro->nombre) !== $normalizar($esperado)) {
            throw new RuntimeException(
                "El registro {$clase} ID {$id} no coincide con '{$esperado}'. "
                . 'No se aplicaron los cambios del seeder. Revise el catálogo actual.'
            );
        }
    }

    private function informarOrdenesIncompatibles(): void
    {
        $incompatibles = 0;

        OrdenTrabajo::with(['tipoProtesis', 'etapaActual'])
            ->orderBy('id')
            ->chunkById(100, function ($ordenes) use (&$incompatibles) {
                foreach ($ordenes as $orden) {
                    $area = (string) $orden->area_trabajo;
                    $tipoValido = $orden->tipoProtesis?->permiteArea($area) ?? false;
                    $etapaValida = !$orden->etapa_actual_id
                        || ($orden->etapaActual?->permiteArea($area) ?? false);

                    if (!$tipoValido || !$etapaValida) {
                        $incompatibles++;
                        $this->command?->warn(
                            "Revisar orden ID {$orden->id} ({$orden->codigo}): "
                            . (!$tipoValido ? 'tipo incompatible con el área. ' : '')
                            . (!$etapaValida ? 'etapa incompatible con el área.' : '')
                        );
                    }
                }
            });

        $this->command?->info("Órdenes que requieren revisión: {$incompatibles}. No se modificaron sus datos.");
    }
}
