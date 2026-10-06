<?php

// Ejecutar desde la raíz del proyecto: php tools/comprobar_reportes.php
// Consulta y renderiza reportes; los datos de prueba no se guardan en la BD.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ReporteAdicionalController;
use App\Http\Controllers\ReporteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;

$admin = User::where('estado', true)->whereHas('role', fn ($r) => $r->where('nombre', 'Administrador'))->first();
if (!$admin) {
    fwrite(STDERR, "Se necesita una cuenta Administrador activa para comprobar las vistas.\n");
    exit(1);
}
Auth::setUser($admin);
View::share('errors', new ViewErrorBag);
$comprobaciones = 0;
$asegurar = function (bool $condicion, string $mensaje) use (&$comprobaciones) {
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
    $comprobaciones++;
};
$solicitud = function (string $nombre, array $parametros = []) use ($app) {
    $ruta = Route::getRoutes()->getByName($nombre);
    if (!$ruta) {
        throw new RuntimeException('Falta la ruta '.$nombre.'. Revise routes/web.php.');
    }
    $request = Request::create('/'.$ruta->uri(), 'GET', $parametros);
    $request->setRouteResolver(fn () => $ruta);
    $app->instance('request', $request);
    Illuminate\Support\Facades\Facade::clearResolvedInstance('request');
    $app->make('url')->setRequest($request);
    return $request;
};

$codigoSalida = 0;
DB::beginTransaction();
try {
    foreach (['inventario', 'calidad', 'pagos', 'mensajeria', 'odontologos'] as $reporte) {
        foreach (['', '.exportar', '.imprimir'] as $sufijo) {
            $nombre = 'reportes.'.$reporte.$sufijo;
            $ruta = Route::getRoutes()->getByName($nombre);
            $asegurar((bool) $ruta, 'Falta '.$nombre);
            $middleware = $ruta->gatherMiddleware();
            $asegurar(in_array('auth', $middleware, true), 'Falta autenticación en '.$nombre);
            $asegurar(in_array('role:Administrador,Recepcion', $middleware, true), 'Falta restricción de roles en '.$nombre);
            $asegurar(($ruta->defaults['reporte'] ?? null) === $reporte, 'Falta el valor predeterminado del reporte en '.$nombre);
        }
    }
    $casos = [
        ['inventario', ['vista' => 'existencias']],
        ['inventario', ['vista' => 'movimientos']],
        ['inventario', ['vista' => 'asignaciones']],
        ['calidad', ['vista' => 'devoluciones']],
        ['calidad', ['vista' => 'garantias']],
        ['calidad', ['vista' => 'repeticiones']],
        ['pagos', ['vista' => 'cobros']],
        ['pagos', ['vista' => 'saldos', 'alcance' => 'todos']],
        ['mensajeria', []],
        ['odontologos', []],
    ];
    $controller = $app->make(ReporteAdicionalController::class);
    foreach ($casos as [$reporte, $parametros]) {
        $vista = $controller->index($solicitud('reportes.'.$reporte, $parametros), $reporte);
        $data = $vista->getData();
        $asegurar(strlen($vista->render()) > 0, 'No se pudo renderizar '.$reporte);
        foreach ($data['filas'] as $fila) {
            foreach ($data['datos']['columnas'] as $columna) {
                $asegurar(array_key_exists($columna['campo'], $fila), 'Columna sin datos en '.$reporte.': '.$columna['campo']);
            }
        }
        $segunda = $controller->index($solicitud('reportes.'.$reporte, $parametros + ['page' => 2]), $reporte)->getData();
        $asegurar($segunda['datos']['metricas'] === $data['datos']['metricas'], 'Los indicadores cambiaron al pasar de página en '.$reporte);
        $impresion = $controller->imprimir($solicitud('reportes.'.$reporte.'.imprimir', $parametros), $reporte);
        $asegurar(count($impresion->getData()['filas']) === $data['filas']->total(), 'La impresión no incluye todos los resultados de '.$reporte);
        $asegurar(strlen($impresion->render()) > 0, 'No se pudo renderizar la impresión de '.$reporte);
        $csv = $controller->exportar($solicitud('reportes.'.$reporte.'.exportar', $parametros), $reporte);
        ob_start();
        try {
            $csv->sendContent();
            $contenido = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $asegurar(str_starts_with($contenido, "\xEF\xBB\xBF"), 'Falta BOM UTF-8 en '.$reporte);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, substr($contenido, 3));
        rewind($stream);
        $cabecera = fgetcsv($stream, 0, ';', '"', '');
        $asegurar($cabecera === array_column($data['datos']['columnas'], 'etiqueta'), 'CSV con encabezados incorrectos en '.$reporte);
        $filasCsv = 0;
        while (($filaCsv = fgetcsv($stream, 0, ';', '"', '')) !== false) {
            $asegurar(count($filaCsv) === count($cabecera), 'CSV con número de columnas incorrecto en '.$reporte);
            $filasCsv++;
        }
        fclose($stream);
        $asegurar($filasCsv === $data['filas']->total(), 'El CSV no incluye todos los resultados de '.$reporte);
        echo 'Correcto: '.$reporte.' / '.($parametros['vista'] ?? 'resumen')."\n";
    }
    try {
        $controller->index($solicitud('reportes.pagos', ['vista' => 'cobros', 'desde' => '2026-10-06', 'hasta' => '2026-10-01']), 'pagos');
        throw new RuntimeException('Se aceptó un período de fechas invertidas.');
    } catch (ValidationException $e) {
        $asegurar(isset($e->errors()['hasta']), 'Error de validación sin mensaje de fecha final.');
    }
    // Comprueba que el nuevo enlace de odontólogos pueda usar el filtro en producción.
    $produccion = $app->make(ReporteController::class);
    $doctor = App\Models\Odontologo::first();
    if ($doctor) {
        $request = $solicitud('reportes.produccion', ['odontologo_id' => $doctor->id]);
        $vista = $produccion->produccion($request);
        $asegurar(strlen($vista->render()) > 0, 'No se pudo renderizar el filtro de odontólogo en producción.');
        foreach ($vista->getData()['ordenes'] as $orden) {
            $asegurar((int) $orden->odontologo_id === (int) $doctor->id, 'El filtro por odontólogo no se aplicó a producción.');
        }
    }
    echo 'Correcto: '.$comprobaciones." comprobaciones. No se guardaron cambios en la base de datos.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'Error: '.$error->getMessage().' en '.$error->getFile().':'.$error->getLine()."\n");
    $codigoSalida = 1;
} finally {
    DB::rollBack();
}

exit($codigoSalida);
