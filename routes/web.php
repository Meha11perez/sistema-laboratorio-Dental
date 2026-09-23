<?php

use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\GarantiaDevolucionController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PagoCreditoController;
use App\Http\Controllers\CuentaOdontologoController;
use App\Http\Controllers\RutaMensajeriaController;
use App\Http\Controllers\DetalleMensajeriaController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('login');});

Route::middleware('auth')->group(function () {
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //ORDENES
    // =====================================================
// ÓRDENES DE TRABAJO
// =====================================================


// -----------------------------------------------------
// ADMINISTRADOR / RECEPCIÓN
// Gestión administrativa de órdenes
// -----------------------------------------------------
    Route::middleware('role:Administrador,Recepcion')->group(function () {

    Route::get('/ordenes/crear', [OrdenTrabajoController::class, 'create'])->name('ordenes.create');
    Route::post( '/ordenes', [OrdenTrabajoController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{orden}/editar', [OrdenTrabajoController::class, 'edit'])->name('ordenes.edit');

    Route::put( '/ordenes/{orden}', [OrdenTrabajoController::class, 'update'])->name('ordenes.update');
    Route::get( '/ordenes/{orden}/cancelar',[OrdenTrabajoController::class, 'confirmarCancelacion'] )->name('ordenes.cancelar.confirmar');
    Route::patch( '/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'] )->name('ordenes.cancelar');
    Route::get( '/ordenes/{orden}/rotulo',[OrdenTrabajoController::class, 'rotulo'])->name('ordenes.rotulo');
    Route::get( '/ordenes/{orden}/repetir', [OrdenTrabajoController::class, 'repetir'])->name('ordenes.repetir');

    // DEVOLUCIONES
    Route::get('/ordenes/{orden}/devolucion/crear',[DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post( '/ordenes/{orden}/devolucion',[DevolucionController::class, 'store'] )->name('devoluciones.store');

    // PAGO DE LA ORDEN
    Route::get('/ordenes/{orden}/pago',[PagoController::class, 'show'] )->name('pagos.show');
    });

    // -----------------------------------------------------
    // ADMINISTRADOR / RECEPCIÓN / TÉCNICO
    // Consulta de órdenes
    // El controlador filtra las órdenes del técnico.
    // -----------------------------------------------------
    Route::middleware('role:Administrador,Recepcion,Técnico')->group(function () {
    Route::get('/ordenes', [OrdenTrabajoController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/{orden}', [OrdenTrabajoController::class, 'show'])->name('ordenes.show');
    });

    // AGENDA
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    
    //inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/crear', [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');    
    Route::get('/inventario/{material}/editar', [InventarioController::class, 'edit'])->name('inventario.edit');
    Route::get('/inventario/{material}/asignar',[InventarioController::class, 'createAsignacion'])
        ->middleware('role:Administrador,Recepcion')
        ->name('inventario.asignar');
    Route::post('/inventario/{material}/asignar',[InventarioController::class, 'storeAsignacion'])
        ->middleware('role:Administrador,Recepcion')
        ->name('inventario.asignar.store');

    Route::get('/inventario/{material}', [InventarioController::class, 'show'])->name('inventario.show');        
    Route::get('/inventario/{material}/movimiento', [InventarioController::class, 'createMovimiento'])->name('inventario.movimiento.create');
    Route::post('/inventario/{material}/movimiento', [InventarioController::class, 'storeMovimiento'])->name('inventario.movimiento.store');
    Route::put('/inventario/{material}', [InventarioController::class, 'update'])->name('inventario.update');
    

    Route::get('/garantias', [GarantiaDevolucionController::class, 'index'])->name('garantias.index');


    Route::view('/reportes', 'reportes.index')->name('reportes.index');

    Route::view('/administracion', 'administracion.index')->name('administracion.index');
    
    //Devoluciones
    Route::get('/ordenes/{orden}/devolucion/crear', [DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post('/ordenes/{orden}/devolucion',[DevolucionController::class, 'store'])->name('devoluciones.store');  
    //Pagos
    Route::view('/pagos', 'pagos.index')->name('pagos.index');
    Route::get('/ordenes/{orden}/pago',[PagoController::class, 'show'])->name('pagos.show');
    Route::put('/pagos/{pago}/monto', [PagoController::class, 'actualizarMonto'])->name('pagos.monto.update');
      //Abonos
    Route::post('/pagos/{pago}/abonos',[AbonoController::class, 'store'])->name('abonos.store');
    //Pagos a crédito
    Route::get('/pagos',[PagoCreditoController::class, 'index'])->name('pagos.index');
    //Cuentas odontólogos
    Route::get('/cuentas-odontologos',[CuentaOdontologoController::class, 'index'])->name('cuentas-odontologos.index');
    Route::get('/odontologos/{odontologo}/cuenta',[CuentaOdontologoController::class, 'edit'])->name('cuentas-odontologos.edit');
    Route::put('/odontologos/{odontologo}/cuenta',[CuentaOdontologoController::class, 'update'])->name('cuentas-odontologos.update');
    Route::get('/odontologos/{odontologo}/cuenta/detalle',[CuentaOdontologoController::class, 'show'])->name('cuentas-odontologos.show');

   // =====================================================
    // MENSAJERÍA
    // =====================================================


    // -----------------------------------------------------
    // PLANIFICACIÓN
    // Solo Administrador y Recepción
    // -----------------------------------------------------
    Route::middleware('role:Administrador,Recepcion')->group(function () {
    Route::get('/mensajeria/crear', [RutaMensajeriaController::class, 'create'])->name('mensajeria.create');
    Route::post('/mensajeria', [RutaMensajeriaController::class, 'store'])->name('mensajeria.store');

    Route::get('/mensajeria/{ruta}/visitas/crear', [DetalleMensajeriaController::class, 'create'])->name('mensajeria.detalles.create');
    Route::post('/mensajeria/{ruta}/visitas', [DetalleMensajeriaController::class, 'store'])->name('mensajeria.detalles.store');
    Route::get('/mensajeria/visitas/{detalle}/reprogramar',[DetalleMensajeriaController::class, 'reprogramarForm'])->name('mensajeria.detalles.reprogramar.form');
    Route::post('/mensajeria/visitas/{detalle}/reprogramar',[DetalleMensajeriaController::class, 'reprogramar'])->name('mensajeria.detalles.reprogramar');
    });


    // -----------------------------------------------------
    // CONSULTA DE MENSAJERÍA
    // Administrador, Recepción y Mensajero
    // -----------------------------------------------------
    Route::middleware('role:Administrador,Recepcion,Mensajero')->group(function () {

        Route::get('/mensajeria', [RutaMensajeriaController::class, 'index'])->name('mensajeria.index');
        Route::get('/mensajeria/entregas', [RutaMensajeriaController::class, 'entregas']) ->name('mensajeria.entregas');
        Route::get('/mensajeria/recolecciones', [RutaMensajeriaController::class, 'recolecciones']) ->name('mensajeria.recolecciones');
        Route::get('/mensajeria/entregas/{detalle}', [RutaMensajeriaController::class, 'showEntrega'])->name('mensajeria.entregas.show');
        Route::get('/mensajeria/recolecciones/{detalle}', [RutaMensajeriaController::class, 'showRecoleccion'])->name('mensajeria.recolecciones.show');
    });


    // -----------------------------------------------------
    // EJECUCIÓN DE RUTA
    // Solo Mensajero
    // -----------------------------------------------------
    Route::middleware('role:Mensajero')->group(function () {
    Route::put( '/mensajeria/{ruta}/iniciar',[RutaMensajeriaController::class, 'iniciar'])->name('mensajeria.iniciar');

    Route::put('/mensajeria/visitas/{detalle}/estado',[DetalleMensajeriaController::class, 'updateEstado'])->name('mensajeria.detalles.estado');
    Route::put( '/mensajeria/{ruta}/finalizar', [RutaMensajeriaController::class, 'finalizar'])->name('mensajeria.finalizar');
    });


    // -----------------------------------------------------
    // DETALLE DE RUTA
    // IMPORTANTE: ESTA DEBE IR AL FINAL
    // -----------------------------------------------------
    Route::middleware('role:Administrador,Recepcion,Mensajero')->group(function () {
     Route::get('/mensajeria/{ruta}', [RutaMensajeriaController::class, 'show'])->name('mensajeria.show');
    });  
    
    Route::middleware('role:Técnico')->group(function () {

    Route::put('/ordenes/{orden}/produccion/iniciar',[OrdenTrabajoController::class, 'iniciarEtapa'])->name('ordenes.produccion.iniciar');

    Route::put('/ordenes/{orden}/produccion/completar',[OrdenTrabajoController::class, 'completarEtapa'])->name('ordenes.produccion.completar');

    Route::post('/ordenes/{orden}/materiales',[OrdenTrabajoController::class, 'registrarMaterial'])->name('ordenes.materiales.store');
    
    });
});

    