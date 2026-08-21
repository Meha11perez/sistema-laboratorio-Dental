<?php

use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\DevolucionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    //ORDENES
    Route::get('/ordenes', [OrdenTrabajoController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/crear', [OrdenTrabajoController::class, 'create'])->name('ordenes.create');  
    Route::post('/ordenes', [OrdenTrabajoController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{orden}', [OrdenTrabajoController::class, 'show'])->name('ordenes.show');
    Route::get('/ordenes/{orden}/editar', [OrdenTrabajoController::class, 'edit'])->name('ordenes.edit');
    Route::put('/ordenes/{orden}', [OrdenTrabajoController::class, 'update'])->name('ordenes.update');
    Route::patch('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'])->name('ordenes.cancelar');
    Route::get('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'confirmarCancelacion'])->name('ordenes.cancelar.confirmar');
    Route::patch('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'])->name('ordenes.cancelar');
    Route::get('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'confirmarCancelacion'])->name('ordenes.cancelar.confirmar');
    Route::patch('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'])->name('ordenes.cancelar');
    Route::get('/ordenes/{orden}/rotulo', [OrdenTrabajoController::class, 'rotulo'])->name('ordenes.rotulo');
    Route::get('/ordenes/{orden}/repetir', [OrdenTrabajoController::class, 'repetir'])->name('ordenes.repetir');
    
    // AGENDA
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    
    //inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/crear', [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');    
    Route::get('/inventario/{material}/editar', [InventarioController::class, 'edit'])->name('inventario.edit');
    Route::get('/inventario/{material}', [InventarioController::class, 'show'])->name('inventario.show');        
    Route::get('/inventario/{material}/movimiento', [InventarioController::class, 'createMovimiento'])->name('inventario.movimiento.create');
    Route::post('/inventario/{material}/movimiento', [InventarioController::class, 'storeMovimiento'])->name('inventario.movimiento.store');
    Route::put('/inventario/{material}', [InventarioController::class, 'update'])->name('inventario.update');
    
    Route::view('/garantias', 'garantias.index')
    ->name('garantias.index');

    Route::view('/pagos', 'pagos.index')
    ->name('pagos.index');

    Route::view('/mensajeria', 'mensajeria.index')
    ->name('mensajeria.index');

    Route::view('/reportes', 'reportes.index')
    ->name('reportes.index');

    Route::view('/administracion', 'administracion.index')
    ->name('administracion.index');
    
    //Devoluciones
    Route::get('/ordenes/{orden}/devolucion/crear', [DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post('/ordenes/{orden}/devolucion',[DevolucionController::class, 'store'])->name('devoluciones.store');  
    
    });

    