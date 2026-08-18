<?php

use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
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
    
    // AGENDA
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    
    //inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/crear', [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');    
    Route::get('/inventario/{material}', [InventarioController::class, 'show'])->name('inventario.show');        
    Route::get('/inventario/{material}/movimiento', [InventarioController::class, 'createMovimiento'])->name('inventario.movimiento.create');
    Route::post('/inventario/{material}/movimiento', [InventarioController::class, 'storeMovimiento'])->name('inventario.movimiento.store');
    });

    