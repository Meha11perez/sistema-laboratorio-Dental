<?php

use App\Http\Controllers\AbonoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ClinicaController;
use App\Http\Controllers\CajaChicaController;
use App\Http\Controllers\CuentaOdontologoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetalleMensajeriaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\GarantiaDevolucionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OdontologoController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PagoCreditoController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ReporteAdicionalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReporteTecnicoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RutaMensajeriaController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// =====================================================
// INICIO
// =====================================================

Route::get('/', function () {
    return redirect()->route('login');
});

// =====================================================
// RUTAS CON AUTENTICACIÓN
// =====================================================

Route::middleware('auth')->group(function () {

    // =================================================
    // DASHBOARD
    // =================================================

    // CAJA CHICA: ADMINISTRADOR Y CONTABILIDAD
    Route::middleware('role:Administrador,Contabilidad')->prefix('caja-chica')->name('caja-chica.')->group(function () {
        Route::get('/', [CajaChicaController::class, 'index'])->name('index');
        Route::get('/crear', [CajaChicaController::class, 'create'])->name('create');
        Route::post('/', [CajaChicaController::class, 'store'])->name('store');
        Route::get('/solicitudes', [CajaChicaController::class, 'solicitudes'])->name('solicitudes');
        Route::get('/exportar', [CajaChicaController::class, 'exportar'])->name('exportar');
        Route::get('/excel', [CajaChicaController::class, 'excel'])->name('excel');
        Route::get('/imprimir', [CajaChicaController::class, 'imprimir'])->name('imprimir');
        Route::get('/correcciones/{correccion}/comprobante', [CajaChicaController::class, 'comprobanteCorreccion'])->name('correcciones.comprobante');
        Route::patch('/correcciones/{correccion}', [CajaChicaController::class, 'resolverCorreccion'])->middleware('role:Administrador')->name('correcciones.resolver');
        Route::get('/{gasto}/comprobante', [CajaChicaController::class, 'comprobante'])->name('comprobante');
        Route::get('/{gasto}/corregir', [CajaChicaController::class, 'solicitarCorreccion'])->name('correcciones.create');
        Route::post('/{gasto}/correcciones', [CajaChicaController::class, 'guardarCorreccion'])->name('correcciones.store');
        Route::patch('/{gasto}/autorizar', [CajaChicaController::class, 'resolver'])->middleware('role:Administrador')->name('resolver');
        Route::get('/{gasto}', [CajaChicaController::class, 'show'])->name('show');
    });

    Route::get('/dashboard', [DashboardController::class,'index',])->name('dashboard');
    Route::view('/ajustes', 'ajustes.index')->name('ajustes.index');

    // CREAR ÓRDENES, RÓTULOS, DEVOLUCIONES Y REPETICIONES
    // ADMINISTRADOR Y RECEPCIÓN
    Route::middleware('role:Administrador,Recepcion')->group(function () {
        Route::get('/ordenes/crear', [OrdenTrabajoController::class, 'create'])->name('ordenes.create');
        Route::post('/ordenes', [OrdenTrabajoController::class, 'store'])->name('ordenes.store');
        Route::get('/ordenes/{orden}/rotulo', [OrdenTrabajoController::class, 'rotulo'])->name('ordenes.rotulo');

        Route::get('/ordenes/{orden}/repetir', [OrdenTrabajoController::class, 'repetir'])->name('ordenes.repetir');

        Route::get('/ordenes/{orden}/devolucion/crear', [DevolucionController::class, 'create'])->name('devoluciones.create');
        Route::post('/ordenes/{orden}/devolucion', [DevolucionController::class, 'store'])->name('devoluciones.store');
    });

    // EDITAR Y CANCELAR ÓRDENES: SOLO ADMINISTRADOR
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/ordenes/{orden}/editar', [OrdenTrabajoController::class, 'edit'])->name('ordenes.edit');
        Route::put('/ordenes/{orden}', [OrdenTrabajoController::class, 'update'])->name('ordenes.update');

        Route::get('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'confirmarCancelacion'])->name('ordenes.cancelar.confirmar');
        Route::patch('/ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'])->name('ordenes.cancelar');
    });

    // =================================================
    // CONSULTA DE ÓRDENES
    // ADMINISTRADOR, RECEPCIÓN Y TÉCNICO
    // =================================================

    Route::middleware('role:Administrador,Recepcion,Técnico,Contabilidad')->group(function () {

        Route::get('/ordenes', [OrdenTrabajoController::class,'index',])->name('ordenes.index');
        Route::get('/ordenes/{orden}', [OrdenTrabajoController::class,'show',])->name('ordenes.show');
    });
    // REVISAR ETAPAS COMPLETADAS: ADMINISTRADOR Y RECEPCIÓN
    Route::middleware('role:Administrador,Recepcion')->group(function () {
        Route::get('/produccion/revisiones', [\App\Http\Controllers\RevisionProduccionController::class, 'index'])->name('produccion.revisiones.index');
        Route::get('/produccion/revisiones/{historial}', [\App\Http\Controllers\RevisionProduccionController::class, 'show'])->name('produccion.revisiones.show');
        Route::patch('/produccion/revisiones/{historial}', [\App\Http\Controllers\RevisionProduccionController::class, 'update'])->name('produccion.revisiones.update');
    });

    // =================================================
    // PRODUCCIÓN
    // =================================================

    Route::get('/produccion', [ProduccionController::class, 'index'])->middleware('role:Administrador,Técnico')->name('produccion.index');

    // =================================================
    // AGENDA
    // =================================================

    Route::get('/agenda', [AgendaController::class, 'index'])->middleware('role:Administrador,Recepcion,Técnico,Contabilidad')->name('agenda.index');

    // =================================================
    // INVENTARIO
    // =================================================

    Route::get('/inventario', [InventarioController::class, 'index'])->middleware('role:Administrador,Técnico,Contabilidad')->name('inventario.index');

    // Estas rutas deben ir antes de /inventario/{material}.

    Route::get('/inventario/tecnicos', [InventarioController::class,'tecnicos',])->middleware('role:Administrador,Contabilidad')->name('inventario.tecnicos');
    Route::get('/inventario/tecnicos/{tecnico}', [InventarioController::class,'tecnicoDetalle',])->middleware('role:Administrador,Contabilidad')->name('inventario.tecnicos.show');
    Route::get('/inventario/movimientos', [ InventarioController::class,'movimientos',])->middleware('role:Administrador,Contabilidad')->name('inventario.movimientos');

    // CREAR MATERIAL

    Route::get('/inventario/crear', [InventarioController::class, 'create'])->middleware('role:Administrador')->name('inventario.create');
    Route::post('/inventario', [InventarioController::class, 'store'])->middleware('role:Administrador')->name('inventario.store');
    
    // ASIGNAR MATERIAL A TÉCNICO

    Route::get('/inventario/{material}/asignar', [InventarioController::class,'createAsignacion',])->middleware('role:Administrador')->name('inventario.asignar');

    Route::post('/inventario/{material}/asignar', [InventarioController::class,'storeAsignacion',])->middleware('role:Administrador')->name('inventario.asignar.store');

    // MOVIMIENTOS DE MATERIAL

    Route::get('/inventario/{material}/movimiento', [InventarioController::class, 'createMovimiento'])->middleware('role:Administrador')->name('inventario.movimiento.create');
    Route::post('/inventario/{material}/movimiento', [InventarioController::class, 'storeMovimiento'])->middleware('role:Administrador')->name('inventario.movimiento.store');
    // EDITAR MATERIAL

    Route::get('/inventario/{material}/editar', [InventarioController::class, 'edit'])->middleware('role:Administrador')->name('inventario.edit');
    Route::put('/inventario/{material}', [InventarioController::class, 'update'])->middleware('role:Administrador')->name('inventario.update');

    // DETALLE DEL MATERIAL

    Route::get('/inventario/{material}', [InventarioController::class, 'show'])->middleware('role:Administrador,Técnico,Contabilidad')->name('inventario.show');

    // =================================================
    // GARANTÍAS Y DEVOLUCIONES
    // =================================================

    Route::get('/garantias', [GarantiaDevolucionController::class, 'index'])->middleware('role:Administrador')->name('garantias.index');

    // =================================================
    // REPORTES: ADMINISTRADOR Y RECEPCIÓN
    // =================================================

    Route::middleware('role:Administrador,Contabilidad')
    ->prefix('reportes')
    ->name('reportes.')
    ->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');

        Route::middleware('role:Administrador')->group(function () {
            Route::get('/produccion', [ReporteController::class, 'produccion'])->name('produccion');
            Route::get('/produccion/exportar', [ReporteController::class, 'exportar'])->name('produccion.exportar');
            Route::get('/produccion/imprimir', [ReporteController::class, 'imprimir'])->name('produccion.imprimir');
            Route::get('/produccion/{orden}/trazabilidad', [ReporteController::class, 'trazabilidad'])->name('trazabilidad');

            Route::get('/tecnicos', [ReporteTecnicoController::class, 'index'])->name('tecnicos');
            Route::get('/tecnicos/exportar', [ReporteTecnicoController::class, 'exportar'])->name('tecnicos.exportar');
            Route::get('/tecnicos/imprimir', [ReporteTecnicoController::class, 'imprimir'])->name('tecnicos.imprimir');
        });

        foreach (['inventario', 'calidad', 'pagos', 'mensajeria', 'odontologos', 'pacientes'] as $reporte) {
            $acceso = in_array($reporte, ['inventario', 'pagos', 'odontologos'], true)
                ? 'role:Administrador,Contabilidad'
                : 'role:Administrador';

            Route::get('/' . $reporte, [ReporteAdicionalController::class, 'index'])->middleware($acceso)->defaults('reporte', $reporte)->name($reporte);

            Route::get('/' . $reporte . '/exportar', [ReporteAdicionalController::class, 'exportar'])->middleware($acceso)->defaults('reporte', $reporte)->name($reporte . '.exportar');
            Route::get('/' . $reporte . '/imprimir', [ReporteAdicionalController::class, 'imprimir'])->middleware($acceso)->defaults('reporte', $reporte)->name($reporte . '.imprimir');
        }
    });

    // =================================================
    // ADMINISTRACIÓN: SOLO ADMINISTRADOR
    // =================================================

    Route::middleware('role:Administrador')->group(function () {

        Route::view('/administracion','administracion.index')->name('administracion.index');
       
        // USUARIOS
        Route::get('/administracion/usuarios', [UserController::class,'index',])->name('administracion.usuarios.index');
        Route::get('/administracion/usuarios/crear', [UserController::class,'create',])->name('administracion.usuarios.create');
        Route::post('/administracion/usuarios', [UserController::class,'store',])->name('administracion.usuarios.store');

        Route::get('/administracion/usuarios/{user}/editar', [UserController::class,'edit',])->name('administracion.usuarios.edit');
        Route::put('/administracion/usuarios/{user}', [UserController::class,'update',])->name('administracion.usuarios.update');
        Route::patch('/administracion/usuarios/{user}/estado', [UserController::class,'toggleEstado',])->name('administracion.usuarios.estado');

        // ROLES
        Route::get('/administracion/roles', [RoleController::class,'index',])->name('administracion.roles.index');
        Route::get('/administracion/roles/{role}/editar', [RoleController::class,'edit',])->name('administracion.roles.edit');
        Route::put('/administracion/roles/{role}', [RoleController::class,'update',])->name('administracion.roles.update');
        Route::patch('/administracion/roles/{role}/estado', [RoleController::class,'toggleEstado',])->name('administracion.roles.estado');

        // TÉCNICOS
        Route::get('/administracion/tecnicos', [TecnicoController::class,'index',])->name('administracion.tecnicos.index');
        Route::get('/administracion/tecnicos/crear', [TecnicoController::class,'create',])->name('administracion.tecnicos.create');
        Route::post('/administracion/tecnicos', [TecnicoController::class,'store',])->name('administracion.tecnicos.store');

        Route::get('/administracion/tecnicos/{tecnico}/editar', [TecnicoController::class,'edit',])->name('administracion.tecnicos.edit');
        Route::put('/administracion/tecnicos/{tecnico}', [TecnicoController::class,'update',])->name('administracion.tecnicos.update');
        Route::patch('/administracion/tecnicos/{tecnico}/estado', [TecnicoController::class,'toggleEstado',])->name('administracion.tecnicos.estado');

    });

    // =================================================
    // CLIENTES: ADMINISTRADOR Y RECEPCIÓN
    // =================================================

    Route::middleware('role:Administrador')->group(function () {

        // ODONTÓLOGOS
        Route::get('/administracion/odontologos', [OdontologoController::class,'index',])->name('administracion.odontologos.index');
        Route::get('/administracion/odontologos/crear', [OdontologoController::class,'create',])->name('administracion.odontologos.create');
        Route::post('/administracion/odontologos', [OdontologoController::class,'store',])->name('administracion.odontologos.store');

        Route::get('/administracion/odontologos/{odontologo}/editar', [OdontologoController::class,'edit',])->name('administracion.odontologos.edit');
        Route::put('/administracion/odontologos/{odontologo}', [OdontologoController::class,'update',])->name('administracion.odontologos.update');
        Route::patch('/administracion/odontologos/{odontologo}/estado', [OdontologoController::class,'toggleEstado',])->name('administracion.odontologos.estado');

        // CLÍNICAS
        Route::get('/administracion/clinicas', [ClinicaController::class,'index',])->name('administracion.clinicas.index');
        Route::get('/administracion/clinicas/crear', [ClinicaController::class,'create',])->name('administracion.clinicas.create');
        Route::post('/administracion/clinicas', [ClinicaController::class,'store',])->name('administracion.clinicas.store');

        Route::get('/administracion/clinicas/{clinica}/editar', [ClinicaController::class,'edit',])->name('administracion.clinicas.edit');
        Route::put('/administracion/clinicas/{clinica}', [ClinicaController::class,'update',])->name('administracion.clinicas.update');
        Route::patch('/administracion/clinicas/{clinica}/estado', [ClinicaController::class,'toggleEstado',])->name('administracion.clinicas.estado');

        // PACIENTES
        // CONSULTAR Y CREAR PACIENTES

        Route::get('/administracion/pacientes/{paciente}/editar', [PacienteController::class,'edit',])->name('administracion.pacientes.edit');
        Route::put('/administracion/pacientes/{paciente}', [PacienteController::class,'update',])->name('administracion.pacientes.update');
        Route::patch('/administracion/pacientes/{paciente}/estado', [PacienteController::class,'toggleEstado',])->name('administracion.pacientes.estado');
    });

        Route::middleware('role:Administrador,Recepcion')->group(function () {
            Route::get('/administracion/pacientes', [PacienteController::class, 'index'])->name('administracion.pacientes.index');
            Route::get('/administracion/pacientes/crear', [PacienteController::class, 'create'])->name('administracion.pacientes.create');
            Route::post('/administracion/pacientes', [PacienteController::class, 'store'])->name('administracion.pacientes.store');
    });
    // =================================================
    // PAGOS
    // =================================================

    // PAGOS Y CUENTAS: ADMINISTRADOR Y CONTABILIDAD
Route::middleware('role:Administrador,Contabilidad')->group(function () {
    Route::get('/pagos', [PagoCreditoController::class, 'index'])->name('pagos.index');
    Route::get('/ordenes/{orden}/pago', [PagoController::class, 'show'])->name('pagos.show');

    Route::post('/pagos/{pago}/abonos', [AbonoController::class, 'store'])->name('abonos.store');
    Route::get('/abonos/{abono}/comprobante', [AbonoController::class, 'comprobante'])->name('abonos.comprobante');

    Route::get('/cuentas-odontologos', [CuentaOdontologoController::class, 'index'])->name('cuentas-odontologos.index');
    Route::get('/odontologos/{odontologo}/cuenta', [CuentaOdontologoController::class, 'edit'])->name('cuentas-odontologos.edit');
    Route::put('/odontologos/{odontologo}/cuenta', [CuentaOdontologoController::class, 'update'])->name('cuentas-odontologos.update');
    Route::get('/odontologos/{odontologo}/cuenta/detalle', [CuentaOdontologoController::class, 'show'])->name('cuentas-odontologos.show');
});

    // MODIFICAR EL MONTO DEL TRABAJO: ADMINISTRADOR
    Route::put('/pagos/{pago}/monto', [PagoController::class, 'actualizarMonto'])->middleware('role:Administrador')->name('pagos.monto.update');

    // =================================================
    // PLANIFICACIÓN DE MENSAJERÍA
    // ADMINISTRADOR Y RECEPCIÓN
    // =================================================

    Route::middleware('role:Administrador,Recepcion')->group(function () {

        Route::get('/mensajeria/crear', [RutaMensajeriaController::class,'create',])->name('mensajeria.create');
        Route::post('/mensajeria', [RutaMensajeriaController::class,'store',])->name('mensajeria.store');

        // VISITAS
        Route::get('/mensajeria/{ruta}/visitas/crear', [DetalleMensajeriaController::class,'create',])->name('mensajeria.detalles.create');
        Route::post('/mensajeria/{ruta}/visitas', [DetalleMensajeriaController::class,'store',])->name('mensajeria.detalles.store');
        Route::get('/mensajeria/visitas/{detalle}/reprogramar', [DetalleMensajeriaController::class,'reprogramarForm',])->name('mensajeria.detalles.reprogramar.form');
        Route::post('/mensajeria/visitas/{detalle}/reprogramar', [DetalleMensajeriaController::class,'reprogramar',
        ])->name('mensajeria.detalles.reprogramar');
    });

    // =================================================
    // CONSULTA DE MENSAJERÍA
    // ADMINISTRADOR, RECEPCIÓN Y MENSAJERO
    // =================================================

    Route::middleware('role:Administrador,Mensajero,Recepcion')->group(function () {

        Route::get('/mensajeria', [RutaMensajeriaController::class,'index',])->name('mensajeria.index');
        Route::get('/mensajeria/entregas', [RutaMensajeriaController::class,'entregas',])->name('mensajeria.entregas');
        Route::get('/mensajeria/recolecciones', [RutaMensajeriaController::class,'recolecciones',])->name('mensajeria.recolecciones');
        Route::get('/mensajeria/entregas/{detalle}', [RutaMensajeriaController::class,'showEntrega',])->name('mensajeria.entregas.show');
        Route::get('/mensajeria/recolecciones/{detalle}', [RutaMensajeriaController::class,'showRecoleccion',])->name('mensajeria.recolecciones.show');
    });

    // =================================================
    // EJECUCIÓN DE RUTA: SOLO MENSAJERO
    // =================================================

    Route::middleware('role:Mensajero')->group(function () {

        Route::put('/mensajeria/{ruta}/iniciar', [RutaMensajeriaController::class,'iniciar',])->name('mensajeria.iniciar');
        Route::put('/mensajeria/visitas/{detalle}/estado', [DetalleMensajeriaController::class,'updateEstado',])->name('mensajeria.detalles.estado');
        Route::put('/mensajeria/{ruta}/finalizar', [RutaMensajeriaController::class,'finalizar',])->name('mensajeria.finalizar');
    });

    // =================================================
    // DETALLE DE RUTA
    // Debe ir después de las rutas fijas de mensajería.
    // =================================================

    Route::middleware('role:Administrador,Mensajero,Recepcion')->group(function () {

        Route::get('/mensajeria/{ruta}', [RutaMensajeriaController::class,'show',])->name('mensajeria.show');
    });

    // =================================================
    // ACCIONES DE PRODUCCIÓN: SOLO TÉCNICO
    // =================================================

    Route::middleware('role:Técnico')->group(function () {

        Route::put('/ordenes/{orden}/produccion/iniciar', [ OrdenTrabajoController::class,'iniciarEtapa',])->name('ordenes.produccion.iniciar');
        Route::put('/ordenes/{orden}/produccion/completar', [OrdenTrabajoController::class,'completarEtapa',])->name('ordenes.produccion.completar');
        Route::post('/ordenes/{orden}/materiales', [OrdenTrabajoController::class,'registrarMaterial',])->name('ordenes.materiales.store');
    });
});