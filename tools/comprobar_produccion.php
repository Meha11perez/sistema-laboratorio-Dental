<?php

// Verificación de solo lectura sobre los catálogos reales y el validador del controlador.
// Ejecutar desde el proyecto: php tools/comprobar_produccion.php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\OrdenTrabajoController;
use App\Models\EtapaProduccion;
use App\Models\TipoProtesis;
use Illuminate\Validation\ValidationException;

$tiposEsperados = [1 => 'fija', 2 => 'removible', 3 => 'removible', 4 => 'removible',
    5 => 'cromo_cobalto', 6 => 'ortodoncia'];
$etapasEsperadas = [
    'removible' => [2, 3, 6],
    'fija' => [4, 5, 6],
    'cromo_cobalto' => [1],
    'ortodoncia' => [7],
];
$fallos = [];
$comprobaciones = 0;
$comprobar = static function (bool $resultado, string $mensaje) use (&$fallos, &$comprobaciones) {
    $comprobaciones++;
    if (!$resultado) $fallos[] = $mensaje;
};

foreach ($tiposEsperados as $id => $area) {
    $tipo = TipoProtesis::find($id);
    $comprobar($tipo && $tipo->area_trabajo === $area, "Área del tipo ID {$id} incorrecta.");
}
$comprobar(TipoProtesis::find(1)?->categoria === 'fija', 'Categoría de Prótesis Fija sin corregir.');

foreach (range(1, 7) as $id) {
    $etapa = EtapaProduccion::find($id);
    if (!$etapa) {
        $comprobar(false, "Falta etapa ID {$id}.");
        continue;
    }
    foreach ($etapasEsperadas as $area => $ids) {
        $comprobar(
            $etapa->permiteArea($area) === in_array($id, $ids, true),
            "Asignación incorrecta: etapa {$id}, área {$area}."
        );
    }
    $comprobar(
        $etapa->es_final === in_array($id, [1, 6, 7], true),
        "Indicador de etapa final incorrecto: {$id}."
    );
}

$controlador = new OrdenTrabajoController();
$metodo = new ReflectionMethod($controlador, 'validarProduccion');
$metodo->setAccessible(true);

foreach ($etapasEsperadas as $area => $idsPermitidos) {
    foreach ($tiposEsperados as $tipoId => $areaTipo) {
        foreach (range(1, 7) as $etapaId) {
            $debeAceptar = $areaTipo === $area && in_array($etapaId, $idsPermitidos, true);
            try {
                $metodo->invoke($controlador, [
                    'area_trabajo' => $area,
                    'tipo_protesis_id' => $tipoId,
                    'etapa_actual_id' => $etapaId,
                    'tecnico_actual_id' => null,
                ]);
                $acepto = true;
            } catch (ValidationException $e) {
                $acepto = false;
            }
            $comprobar($acepto === $debeAceptar, "Combinación incorrecta: {$area}, tipo {$tipoId}, etapa {$etapaId}.");
        }
    }
}

// Una orden puede quedar pendiente sin etapa ni técnico; asignar técnico exige etapa.
foreach ($tiposEsperados as $tipoId => $area) {
    try {
        $metodo->invoke($controlador, [
            'area_trabajo' => $area,
            'tipo_protesis_id' => $tipoId,
            'etapa_actual_id' => null,
            'tecnico_actual_id' => null,
        ]);
        $comprobar(true, '');
    } catch (ValidationException $e) {
        $comprobar(false, "No permitió orden pendiente sin etapa: tipo {$tipoId}.");
    }

    try {
        $metodo->invoke($controlador, [
            'area_trabajo' => $area,
            'tipo_protesis_id' => $tipoId,
            'etapa_actual_id' => null,
            'tecnico_actual_id' => 1,
        ]);
        $comprobar(false, "Permitió técnico sin etapa: tipo {$tipoId}.");
    } catch (ValidationException $e) {
        $comprobar(isset($e->errors()['etapa_actual_id']), "Error asociado al campo incorrecto: tipo {$tipoId}.");
    }
}

if ($fallos) {
    fwrite(STDERR, implode(PHP_EOL, $fallos) . PHP_EOL);
    exit(1);
}
echo "Correcto: {$comprobaciones} comprobaciones de catálogos y validación. No se modificó la base de datos." . PHP_EOL;
