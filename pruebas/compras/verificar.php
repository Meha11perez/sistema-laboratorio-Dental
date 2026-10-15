<?php
/** Pruebas aisladas: SQLite en memoria y archivos temporales; no carga .env ni la base del sistema. */
$raiz = dirname(__DIR__, 2);
$loader = require $raiz.'/vendor/autoload.php';
$loader->addPsr4('App\\', $raiz.'/app', true);

use App\Http\Controllers\CompraController;
use App\Models\{Compra, CompraCorreccion, CompraDetalle, CompraHistorial, Material, MovimientoInventario};
use App\Services\RegistroCompra;
use App\Models\{CajaChicaCorreccion, CajaChicaGasto, CajaChicaPeriodo, Role, User};
use App\Services\CajaChica;
use App\Support\ImporteCajaChica as Importe;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Facades\{DB, Facade, Route, Schema, Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

$temporal = sys_get_temp_dir().'/prueba-compras-'.bin2hex(random_bytes(6));
foreach (['compiled', 'fixture/layouts', 'private'] as $carpeta) mkdir($temporal.'/'.$carpeta, 0700, true);
file_put_contents($temporal.'/fixture/layouts/app.blade.php', '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/tailwind.css"><style>body{margin:0;background:#f5f7fb;font-family:Arial,sans-serif}main{padding:24px;min-width:0}</style></head><body><main>@yield("content")</main></body></html>');
$app = new Application($raiz);
$app->instance('config', new Repository([
    'app' => ['locale'=>'es','fallback_locale'=>'es','url'=>'http://localhost','timezone'=>'America/Guatemala','key'=>'base64:'.base64_encode(str_repeat('a',32))],
    'database' => ['default'=>'sqlite','connections'=>['sqlite'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'','foreign_key_constraints'=>true]]],
    'session' => ['driver'=>'array','lifetime'=>120,'cookie'=>'prueba-caja','path'=>'/','lottery'=>[0,100]],
    'view' => ['paths'=>[$temporal.'/fixture', $raiz.'/resources/views'],'compiled'=>$temporal.'/compiled'],
    'filesystems' => ['default'=>'local','disks'=>['local'=>['driver'=>'local','root'=>$temporal.'/private','throw'=>true]]],
    'hashing' => ['driver'=>'bcrypt','bcrypt'=>['rounds'=>4]],
]));
$app->instance('request', Request::create('http://localhost'));
Facade::setFacadeApplication($app);
foreach ([
    Illuminate\Filesystem\FilesystemServiceProvider::class, Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class, Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class, Illuminate\Session\SessionServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class, Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
] as $proveedor) $app->register($proveedor);
$app->boot();
date_default_timezone_set('America/Guatemala');
Carbon\Carbon::setTestNow(Carbon\Carbon::parse('2026-10-15 09:00:00', 'America/Guatemala'));
$app['view']->share('errors', new Illuminate\Support\ViewErrorBag());
require $raiz.'/routes/web.php';
$app['router']->getRoutes()->refreshNameLookups();
if (!Route::has('compras.index')) throw new RuntimeException('Faltan las rutas de Compras. Añada su bloque en routes/web.php o cargue routes/compras.php una sola vez.');
$app['router']->get('/login', fn()=>null)->name('login');
$app['router']->post('/logout', fn()=>null)->name('logout');
$app['router']->getRoutes()->refreshNameLookups();

(require $raiz.'/database/migrations/0000_12_31_235959_create_roles_table.php')->up();
(require $raiz.'/database/migrations/0001_01_01_000000_create_users_table.php')->up();
Schema::table('users', fn($t) => $t->string('username')->nullable()->unique());
foreach (['ordenes_trabajo','pagos','abonos','clinicas','odontologos','pacientes','tecnicos'] as $tabla) {
    Schema::create($tabla, function($t) { $t->id(); $t->string('dato_original'); });
    DB::table($tabla)->insert(['dato_original'=>'REGISTRO ORIGINAL DE PRUEBA']);
}
(require $raiz.'/database/migrations/2026_08_07_045056_create_materials_table.php')->up();
(require $raiz.'/database/migrations/2026_08_07_050236_create_movimiento_inventarios_table.php')->up();
$tablasAntes = Schema::getTableListing();
$migracion = require $raiz.'/database/migrations/2026_10_15_000001_crear_compras.php';
$migracion->up();
$migracionMaterial = require $raiz.'/database/migrations/2026_10_15_000002_agregar_material_solicitado_a_compra_detalles.php';
$migracionMaterial->up();
$usuarios=[];
foreach (['Administrador','Contabilidad','Recepcion','Técnico','Mensajero'] as $nombre) {
    $rol=Role::create(['nombre'=>$nombre,'estado'=>true]);
    $usuarios[$nombre]=User::create(['role_id'=>$rol->id,'estado'=>true,'name'=>'Prueba '.$nombre,'email'=>'prueba'.$rol->id.'@example.test','password'=>'clave-solo-de-prueba']);
}
$actor=$usuarios['Contabilidad'];
$controlador = new CompraController;
$cuenta = 0;
function comprobar(bool $valor, string $mensaje): void { global $cuenta; if (!$valor) throw new RuntimeException($mensaje); $cuenta++; }
function rechazar(callable $accion, int $codigo = 422): void {
    global $cuenta;
    try { $accion(); } catch (ValidationException $e) { if ($codigo !== 422) throw $e; $cuenta++; return; }
    catch (HttpException $e) { if ($e->getStatusCode() !== $codigo) throw $e; $cuenta++; return; }
    catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { if ($codigo !== 404) throw $e; $cuenta++; return; }
    throw new RuntimeException('Se permitió una operación que debía rechazarse.');
}
function solicitud(array $datos = [], string $metodo = 'POST', array $archivos = []): Request {
    global $app, $actor;
    $r = Request::create('http://localhost/compras', $metodo, $datos, [], $archivos);
    $r->setLaravelSession($app['session']->driver()); $r->setUserResolver(fn () => $actor);
    $r->setRouteResolver(fn () => $app['router']->getRoutes()->match($r));
    $app->instance('request', $r); $app->instance('auth', new Illuminate\Auth\RequestGuard(fn () => $actor, $r));
    return $r;
}
function datos(array $cambios = []): array {
    global $material;
    return array_replace(['clave_registro'=>(string) Str::uuid(), 'fecha'=>'2026-10-15', 'proveedor'=>'Proveedor de prueba', 'nit'=>'1234567-8', 'serie'=>'A',
        'numero_factura'=>'F-'.Str::uuid(), 'metodo_pago'=>'Efectivo', 'referencia'=>null, 'observaciones'=>'Solo datos ficticios',
        'detalles'=>[['tipo'=>'Material', 'material_id'=>$material->id, 'descripcion'=>'Yeso según factura', 'cantidad'=>'2.50', 'precio'=>'10.01'],
            ['tipo'=>'Servicio', 'material_id'=>null, 'descripcion'=>'Servicio de mantenimiento', 'cantidad'=>'1', 'precio'=>'25.50']]], $cambios);
}
function compra(array $cambios = []): Compra { global $actor; return RegistroCompra::registrar(datos($cambios), $actor, 'comprobantes/compras/factura-prueba.pdf'); }
function propuesta(Compra $compra, array $cambios = []): array {
    $s = $compra->fresh()->instantanea();
    $s['detalles'] = array_map(fn ($d) => ['tipo'=>$d['tipo'], 'material_id'=>$d['material_id'] ?? (!empty($d['material_solicitado']) ? 'nuevo' : null), 'material_nombre'=>$d['material_solicitado'] ?? null, 'material_unidad'=>$d['unidad'], 'descripcion'=>$d['descripcion'],
        'cantidad'=>Importe::decimal($d['cantidad_centesimas']), 'precio'=>Importe::decimal($d['precio_centavos'])], $s['detalles']);
    return array_replace($s, ['clave_registro'=>(string) Str::uuid(), 'tipo'=>'Corregir', 'version_compra'=>$compra->fresh()->version, 'motivo'=>'Corregir según factura original.'], $cambios);
}
function vista(string $nombre, $vista): string {
    global $evidencias;
    $html = $vista->render();
    comprobar(!str_contains($html, '@extends') && !str_contains($html, '<?php'), 'Vista sin compilar '.$nombre);
    if ($evidencias) file_put_contents($evidencias.'/'.$nombre.'.html', $html);
    return $html;
}
$evidencias = getenv('COMPRAS_EVIDENCIAS') ?: null;
if ($evidencias && !is_dir($evidencias)) mkdir($evidencias, 0700, true);
solicitud();
$material = Material::create(['nombre'=>'Yeso de prueba', 'unidad_medida'=>'kg', 'codigo'=>'PRUEBA-MAT-001', 'estado'=>true, 'stock_actual'=>'10.00', 'stock_minimo'=>'1.00', 'costo_unitario'=>'8.00']);
$otro = Material::create(['nombre'=>'Resina de prueba', 'unidad_medida'=>'unidad', 'codigo'=>'PRUEBA-MAT-002', 'estado'=>true, 'stock_actual'=>'20.00', 'stock_minimo'=>'1.00', 'costo_unitario'=>'12.00']);
comprobar(count(array_diff(Schema::getTableListing(), $tablasAntes)) === 4, 'La migración no se limita a cuatro tablas nuevas.');
$d = datos(['total_centavos'=>1, 'estado'=>'Anulada', 'registrado_por'=>999]);
$c = RegistroCompra::registrar($d, $actor, 'comprobantes/compras/factura-prueba.pdf');
comprobar($c->total_centavos === 5053 && $c->estado === 'Registrada' && $c->registrado_por === $actor->id, 'Total o actor manipulables.');
comprobar($c->nit === '12345678' && $c->historiales()->count() === 1 && $c->detalles()->count() === 2, 'Registro sin datos normalizados o historial.');
comprobar($material->fresh()->stock_actual === '10.00' && MovimientoInventario::count() === 0, 'Registrar compra modifica stock antes de recibir.');
comprobar(RegistroCompra::registrar($d, $actor, 'comprobantes/compras/otra.pdf')->id === $c->id && Compra::count() === 1, 'Reintentar duplica la compra.');
rechazar(fn () => compra(['nit'=>'12345678', 'serie'=>$c->serie, 'numero_factura'=>$c->numero_factura]));
foreach (['0', '-1', '1.001', '1e3', '1,50', '9999999'] as $valor) {
    $datos = datos(); $datos['detalles'][0]['cantidad'] = $valor;
    rechazar(fn () => RegistroCompra::registrar($datos, $actor, 'comprobantes/compras/a.pdf'));
    $datos = datos(); $datos['detalles'][0]['precio'] = $valor;
    rechazar(fn () => RegistroCompra::registrar($datos, $actor, 'comprobantes/compras/a.pdf'));
}
foreach ([['fecha'=>'2026-10-16'], ['fecha'=>'2026-02-30'], ['nit'=>'  '], ['proveedor'=>'  '], ['numero_factura'=>' '], ['metodo_pago'=>'Tarjeta no configurada'], ['metodo_pago'=>'Transferencia','referencia'=>''], ['detalles'=>[]]] as $cambios) rechazar(fn () => compra($cambios));
$datos=datos(); unset($datos['detalles'][0]['material_id']); rechazar(fn () => RegistroCompra::registrar($datos,$actor,'comprobantes/compras/a.pdf'));
$datos=datos(); $datos['detalles'][0]['cantidad']='0.01'; $datos['detalles'][0]['precio']='0.01'; rechazar(fn () => RegistroCompra::registrar($datos,$actor,'comprobantes/compras/a.pdf'));
$otro->update(['estado'=>false]); $datos=datos(); $datos['detalles'][0]['material_id']=$otro->id; rechazar(fn () => RegistroCompra::registrar($datos,$actor,'comprobantes/compras/a.pdf')); $otro->update(['estado'=>true]);
foreach (['Recepcion','Técnico','Mensajero'] as $rol) {
    $actor=$usuarios[$rol]; rechazar(fn () => RegistroCompra::registrar(datos(),$actor,'comprobantes/compras/a.pdf'),403);
    rechazar(fn () => $controlador->index(solicitud([], 'GET')),403);
    rechazar(fn () => $controlador->comprobante(solicitud([], 'GET'),$c),403);
}
$actor=$usuarios['Contabilidad']; rechazar(fn () => RegistroCompra::recibir($c->id,1,'Materiales recibidos.', $actor),403);
$s=RegistroCompra::solicitar($c->id,propuesta($c,['proveedor'=>'Proveedor corregido']),$actor,null);
comprobar($c->fresh()->proveedor === 'Proveedor de prueba', 'La solicitud cambia el original antes de aprobar.');
rechazar(fn () => RegistroCompra::solicitar($c->id,propuesta($c,['proveedor'=>'Otro cambio']),$actor,null));
rechazar(fn () => RegistroCompra::resolver($s->id,'aprobar','Factura verificada.', $actor),403);
$actor=$usuarios['Administrador']; rechazar(fn () => RegistroCompra::recibir($c->id,1,'Materiales recibidos.', $actor));
RegistroCompra::resolver($s->id,'aprobar','Factura verificada.', $actor);
comprobar($c->fresh()->proveedor === 'Proveedor corregido' && $s->fresh()->estado === 'Aprobada' && $s->fresh()->aplicados['proveedor'] === 'Proveedor corregido', 'Corrección no aplicada o historial incompleto.');
rechazar(fn () => RegistroCompra::resolver($s->id,'aprobar','Segunda decisión.', $actor));
rechazar(fn () => RegistroCompra::recibir($c->id,1,'Materiales recibidos.', $actor));
$antesMaterial=$material->fresh()->toArray();
RegistroCompra::recibir($c->id,$c->fresh()->version,'Se recibieron todos los materiales.', $actor);
comprobar($material->fresh()->stock_actual === '12.50' && MovimientoInventario::count() === 1, 'Recepción no suma la cantidad exacta una vez.');
comprobar($material->fresh()->costo_unitario === $antesMaterial['costo_unitario'], 'La recepción cambia el costo preexistente sin autorización.');
comprobar($c->fresh()->fecha_recepcion !== null && $c->fresh()->recibido_por === $actor->id && $c->detalles()->whereNotNull('movimiento_entrada_id')->count() === 1, 'Recepción sin trazabilidad.');
$movimiento=MovimientoInventario::first(); comprobar($movimiento->stock_anterior==='10.00' && $movimiento->stock_nuevo==='12.50' && $movimiento->cantidad==='2.50', 'Movimiento incoherente con inventario.');
RegistroCompra::recibir($c->id,1,'Reintento de recepción.', $actor);
comprobar($material->fresh()->stock_actual === '12.50' && MovimientoInventario::count() === 1, 'Reintentar recepción duplica el stock.');
rechazar(fn () => RegistroCompra::solicitar($c->id,propuesta($c,['tipo'=>'Anular']),$actor,null));
$datos=propuesta($c); $datos['detalles'][0]['cantidad']='3'; rechazar(fn () => RegistroCompra::solicitar($c->id,$datos,$actor,null));
$datos=propuesta($c); $datos['detalles'][0]['material_id']=$otro->id; rechazar(fn () => RegistroCompra::solicitar($c->id,$datos,$actor,null));
$datos=propuesta($c); $datos['detalles'][0]['precio']='11.00';
$s2=RegistroCompra::solicitar($c->id,$datos,$actor,'comprobantes/compras/factura-corregida.pdf');
RegistroCompra::resolver($s2->id,'aprobar','Precio confirmado en factura.', $actor);
comprobar($c->fresh()->total_centavos===5300 && $material->fresh()->stock_actual==='12.50' && MovimientoInventario::count()===1 && $c->detalles()->whereNotNull('movimiento_entrada_id')->count()===1,'Corregir precio altera stock o pierde entrada.');
$servicio=compra(['detalles'=>[['tipo'=>'Servicio','descripcion'=>'Servicio de prueba','cantidad'=>'1','precio'=>'100.00']]]);
rechazar(fn () => RegistroCompra::recibir($servicio->id,1,'Recibir servicio.', $actor));
$sa=RegistroCompra::solicitar($servicio->id,propuesta($servicio,['tipo'=>'Anular']),$actor,null);
RegistroCompra::resolver($sa->id,'rechazar','La factura sí corresponde.', $actor);
comprobar($servicio->fresh()->estado==='Registrada' && $sa->fresh()->aplicados===null,'Rechazar anula la compra.');
$sa=RegistroCompra::solicitar($servicio->id,propuesta($servicio,['tipo'=>'Anular']),$actor,null);
RegistroCompra::resolver($sa->id,'aprobar','Anular factura duplicada de prueba.', $actor);
comprobar($servicio->fresh()->estado==='Anulada' && $servicio->detalles()->count()===1 && $servicio->historiales()->count()>=5,'La anulación elimina datos o historial.');
rechazar(fn () => RegistroCompra::solicitar($servicio->id,propuesta($servicio),$actor,null));
$datos=datos(); $datos['detalles'][1]=['tipo'=>'Material','material_id'=>$otro->id,'descripcion'=>'Resina','cantidad'=>'2','precio'=>'1']; $doble=RegistroCompra::registrar($datos,$actor,'comprobantes/compras/a.pdf');
$otro->update(['estado'=>false]); $antes=$material->fresh()->stock_actual; $movimientos=MovimientoInventario::count();
rechazar(fn () => RegistroCompra::recibir($doble->id,1,'Verificar recepción.', $actor));
comprobar($material->fresh()->stock_actual===$antes && MovimientoInventario::count()===$movimientos && $doble->fresh()->fecha_recepcion===null,'Una recepción fallida deja entradas parciales.'); $otro->update(['estado'=>true]);
$otro->update(['stock_actual'=>'99999999.99']); rechazar(fn () => RegistroCompra::recibir($doble->id,1,'Verificar recepción.', $actor));
comprobar($material->fresh()->stock_actual===$antes && MovimientoInventario::count()===$movimientos,'Desbordamiento dejó cambios parciales.'); $otro->update(['stock_actual'=>'20.00']);
RegistroCompra::recibir($doble->id,1,'Recepción completa verificada.', $actor);
comprobar($material->fresh()->stock_actual==='15.00' && $otro->fresh()->stock_actual==='22.00','No se reciben correctamente distintos materiales.');
$datos=datos(); $datos['detalles'][1]=$datos['detalles'][0]; $repetido=RegistroCompra::registrar($datos,$actor,'comprobantes/compras/a.pdf');
RegistroCompra::recibir($repetido->id,1,'Dos líneas del mismo material.', $actor);
comprobar($material->fresh()->stock_actual==='20.00','Dos líneas del mismo material pierden una entrada.');
// Adjuntos privados y limpieza de un envío duplicado.
file_put_contents($temporal.'/factura.pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF");
$actor=$usuarios['Contabilidad']; $datos=datos(['numero_factura'=>'000000001']);
$archivo=fn () => new UploadedFile($temporal.'/factura.pdf','factura.pdf','application/pdf',null,true);
rechazar(fn () => $controlador->store(solicitud($datos)));
$controlador->store(solicitud($datos,'POST',['factura'=>$archivo()]));
$adjunta=Compra::where('clave_registro',$datos['clave_registro'])->firstOrFail();
$archivos=Storage::disk('local')->allFiles();
$controlador->store(solicitud($datos,'POST',['factura'=>$archivo()]));
comprobar(Storage::disk('local')->allFiles()===$archivos && Compra::where('clave_registro',$datos['clave_registro'])->count()===1,'Reenvío deja adjunto huérfano o factura duplicada.');
comprobar(str_starts_with($adjunta->factura_path,'comprobantes/compras/') && Storage::disk('local')->exists($adjunta->factura_path),'Factura no guardada en almacenamiento privado.');
$respuesta=$controlador->comprobante(solicitud([], 'GET'),$adjunta);
comprobar(str_contains($respuesta->headers->get('Content-Disposition'),'attachment') && str_contains($respuesta->headers->get('Cache-Control'),'private'),'La factura se entrega sin protección de descarga.');
rechazar(fn () => $controlador->comprobante(solicitud(['correccion'=>$s->id], 'GET'),$adjunta),404);
$desactivado=$usuarios['Contabilidad']; $desactivado->update(['estado'=>false]); rechazar(fn () => $controlador->index(solicitud([], 'GET')),403); $desactivado->update(['estado'=>true]);
rechazar(fn () => $controlador->index(solicitud(['mes'=>'2026-99'], 'GET')));
$lista=$controlador->index(solicitud(['mes'=>'2026-10','estado'=>'Anulada'], 'GET'))->getData();
comprobar($lista['compras']->total()===1 && $lista['totalMes']===(int) Compra::where('estado','Registrada')->sum('total_centavos'),'Los filtros alteran el total del mes o suman anuladas.');
foreach (['Recepcion','Técnico','Mensajero'] as $rol) { $actor=$usuarios[$rol]; rechazar(fn () => $controlador->recibir(solicitud(),$adjunta),403); rechazar(fn () => $controlador->resolver(solicitud(),$s),403); }
$actor=$usuarios['Contabilidad']; rechazar(fn () => $controlador->recibir(solicitud(),$adjunta),403); rechazar(fn () => $controlador->resolver(solicitud(),$s),403);
// Rutas concretas y protección de acciones, también si se escribe la URL.
foreach (['/compras'=>'compras.index','/compras/crear'=>'compras.create','/compras/solicitudes'=>'compras.solicitudes'] as $url=>$nombre) {
    $ruta=$app['router']->getRoutes()->match(Request::create($url)); comprobar($ruta->getName()===$nombre && in_array('auth',$ruta->gatherMiddleware()) && in_array('role:Administrador,Contabilidad',$ruta->gatherMiddleware()),'Ruta sin acceso correcto '.$url);
}
foreach (['/compras/1/recibir'=>'POST','/compras/solicitudes/1'=>'PATCH'] as $url=>$metodo) {
    $ruta=$app['router']->getRoutes()->match(Request::create($url,$metodo)); comprobar(in_array('role:Administrador',$ruta->gatherMiddleware()),'Acción de stock o aprobación sin restricción al Administrador.');
}
foreach (['Administrador','Contabilidad'] as $rol) {
    $actor=$usuarios[$rol]; solicitud([], 'GET');
    vista('index-'.strtolower($rol),$controlador->index(solicitud([], 'GET')));
    vista('crear-'.strtolower($rol),$controlador->create(solicitud([], 'GET')));
    vista('corregir-'.strtolower($rol),$controlador->corregir(solicitud([], 'GET'),$c->fresh()));
    $html=vista('detalle-recibida-'.strtolower($rol),$controlador->show(solicitud([], 'GET'),$c->fresh()));
    comprobar(!str_contains($html,'Confirmar entrada al inventario'),'Una compra recibida ofrece volver a recibir.');
    $html=vista('detalle-pendiente-'.strtolower($rol),$controlador->show(solicitud([], 'GET'),$adjunta->fresh()));
    comprobar(str_contains($html,'Confirmar entrada al inventario')===($rol==='Administrador'),'Contabilidad puede recibir desde la vista.');
    vista('solicitudes-'.strtolower($rol),$controlador->solicitudes(solicitud([], 'GET')));
}
$actor=$usuarios['Contabilidad'];
$sinCambios=propuesta($adjunta); rechazar(fn () => RegistroCompra::solicitar($adjunta->id,$sinCambios,$actor,null));
$obsoleta=propuesta($adjunta,['version_compra'=>999,'proveedor'=>'Dato desde pestaña antigua']); rechazar(fn () => RegistroCompra::solicitar($adjunta->id,$obsoleta,$actor,null));
$existe=compra(['numero_factura'=>'FACTURA-EXISTENTE']);
$duplicada=RegistroCompra::solicitar($adjunta->id,propuesta($adjunta,['numero_factura'=>$existe->numero_factura]),$actor,null);
rechazar(fn () => RegistroCompra::resolver($duplicada->id,'aprobar','Intento sin rol autorizado.', $actor),403);
$actor=$usuarios['Administrador']; rechazar(fn () => RegistroCompra::resolver($duplicada->id,'aprobar','Comparación de factura repetida.', $actor));
comprobar($duplicada->fresh()->estado==='Pendiente' && $adjunta->fresh()->numero_factura==='000000001','Una factura duplicada deja cambios parciales al aprobar.');
RegistroCompra::resolver($duplicada->id,'rechazar','El número propuesto ya está utilizado.', $actor);
$actor=$usuarios['Contabilidad'];
$pend=RegistroCompra::solicitar($adjunta->id,propuesta($adjunta,['proveedor'=>'Proveedor pendiente de corrección']),$actor,null);
foreach (['Administrador','Contabilidad'] as $rol) {
    $actor=$usuarios[$rol];
    $html=vista('decision-'.strtolower($rol),$controlador->show(solicitud([], 'GET'),$adjunta->fresh()));
    comprobar(str_contains($html,'Aprobar solicitud')===($rol==='Administrador'),'Contabilidad tiene botones de decisión.');
}
foreach (['ordenes_trabajo','pagos','abonos','clinicas','odontologos','pacientes','tecnicos'] as $tabla) {
    comprobar(DB::table($tabla)->count()===1 && DB::table($tabla)->first()->dato_original==='REGISTRO ORIGINAL DE PRUEBA','Se modificaron registros ajenos de '.$tabla);
}
// Materiales propuestos: Contabilidad registra, el Administrador crea al recibir.
$actor=$usuarios['Contabilidad'];
$cantidadMateriales=Material::count();
$nuevoDato=datos(['detalles'=>[['tipo'=>'Material','material_id'=>'nuevo','material_nombre'=>'  Resina   nueva de prueba  ','material_unidad'=>'unidad','descripcion'=>'Resina según factura','cantidad'=>'3','precio'=>'12.50']]]);
$nueva=RegistroCompra::registrar($nuevoDato,$actor,'comprobantes/compras/nuevo.pdf');
comprobar(Material::count()===$cantidadMateriales && $nueva->detalles->first()->material_id===null && $nueva->detalles->first()->material_solicitado==='Resina nueva de prueba','Registrar solicitud crea un material o pierde su nombre.');
comprobar($nueva->historiales->first()->datos['detalles'][0]['material_solicitado']==='Resina nueva de prueba','No se conserva el nombre propuesto en el historial.');
rechazar(fn () => RegistroCompra::recibir($nueva->id,1,'Recibir material nuevo.', $actor),403);
foreach (['material_nombre','material_unidad'] as $campo) {
    $invalida=$nuevoDato; $invalida['clave_registro']=(string) Str::uuid(); $invalida['numero_factura']=(string) Str::uuid(); $invalida['detalles'][0][$campo]='  ';
    rechazar(fn () => RegistroCompra::registrar($invalida,$actor,'comprobantes/compras/nuevo.pdf'));
}
$actor=$usuarios['Administrador'];
RegistroCompra::recibir($nueva->id,1,'Nombre y unidad revisados; material recibido.', $actor);
$creado=Material::where('nombre','Resina nueva de prueba')->firstOrFail();
comprobar((bool) preg_match('/^MAT-\d{3,}$/',$creado->codigo),'Un material nuevo conserva el código largo.');
comprobar(Material::count()===$cantidadMateriales+1 && $creado->stock_actual==='3.00' && $creado->costo_unitario==='12.50','El nuevo material no tiene existencia y costo inicial correctos.');
comprobar($nueva->detalles()->first()->material_id===$creado->id && $nueva->detalles()->first()->movimiento_entrada_id!==null,'El material creado no quedó vinculado.');
$eventos=MovimientoInventario::count(); RegistroCompra::recibir($nueva->id,1,'Reintento de alta y recepción.', $actor);
comprobar(Material::count()===$cantidadMateriales+1 && $creado->fresh()->stock_actual==='3.00' && MovimientoInventario::count()===$eventos,'Reintentar duplica material o entrada.');
$siguiente=$nuevoDato; $siguiente['clave_registro']=(string) Str::uuid(); $siguiente['numero_factura']=(string) Str::uuid(); $siguiente['detalles'][0]['precio']='15.00';
$reutiliza=RegistroCompra::registrar($siguiente,$actor,'comprobantes/compras/nuevo.pdf');
RegistroCompra::recibir($reutiliza->id,1,'El material ya existe; reutilizar catálogo.', $actor);
comprobar(Material::count()===$cantidadMateriales+1 && $creado->fresh()->stock_actual==='6.00' && $creado->fresh()->costo_unitario==='12.50','La segunda compra duplica catálogo o cambia el costo existente.');
$otra=$siguiente; $otra['clave_registro']=(string) Str::uuid(); $otra['numero_factura']=(string) Str::uuid(); $otra['detalles'][0]['material_unidad']='kg';
$incompatible=RegistroCompra::registrar($otra,$actor,'comprobantes/compras/nuevo.pdf');
rechazar(fn () => RegistroCompra::recibir($incompatible->id,1,'Intento con unidad incompatible.', $actor));
comprobar($creado->fresh()->stock_actual==='6.00' && $incompatible->fresh()->fecha_recepcion===null && $incompatible->detalles()->first()->material_id===null,'La unidad incompatible deja cambios parciales.');
$creado->update(['estado'=>false]); $inactiva=$siguiente; $inactiva['clave_registro']=(string) Str::uuid(); $inactiva['numero_factura']=(string) Str::uuid();
$paraInactivo=RegistroCompra::registrar($inactiva,$actor,'comprobantes/compras/nuevo.pdf'); rechazar(fn () => RegistroCompra::recibir($paraInactivo->id,1,'Material ya existente e inactivo.', $actor));
comprobar(!$creado->fresh()->estado && Material::count()===$cantidadMateriales+1,'Recibir reactiva o duplica un material inactivo.');
$mezcla=$nuevoDato; $mezcla['clave_registro']=(string) Str::uuid(); $mezcla['numero_factura']=(string) Str::uuid(); $mezcla['detalles'][0]['material_nombre']='Material a descartar si falla';
$mezcla['detalles'][]=['tipo'=>'Material','material_id'=>$creado->id,'descripcion'=>'Material inactivo al recibir','cantidad'=>'1','precio'=>'1.00'];
// Registrar cuando aún está activo y desactivarlo antes de la recepción.
$creado->update(['estado'=>true]); $fallida=RegistroCompra::registrar($mezcla,$actor,'comprobantes/compras/nuevo.pdf'); $creado->update(['estado'=>false]);
$antesMovimientos=MovimientoInventario::count(); rechazar(fn () => RegistroCompra::recibir($fallida->id,1,'No debe dejar un alta parcial.', $actor));
comprobar(!Material::where('nombre','Material a descartar si falla')->exists() && MovimientoInventario::count()===$antesMovimientos && $fallida->fresh()->fecha_recepcion===null,'La recepción fallida deja un material o una entrada parcial.');
$creado->update(['estado'=>true]);
$precioNuevo=propuesta($nueva); $precioNuevo['detalles'][0]['precio']='13.00'; $corNuevo=RegistroCompra::solicitar($nueva->id,$precioNuevo,$actor,null);
RegistroCompra::resolver($corNuevo->id,'aprobar','Precio revisado después de recibir.', $actor);
comprobar($nueva->fresh()->total_centavos===3900 && $creado->fresh()->stock_actual==='6.00' && $nueva->detalles()->first()->material_solicitado==='Resina nueva de prueba','La corrección pierde la solicitud de alta o vuelve a sumar stock.');
$actor=$usuarios['Contabilidad']; vista('corregir-material-nuevo',$controlador->corregir(solicitud([], 'GET'),$incompatible->fresh()));
$html=vista('detalle-material-nuevo',$controlador->show(solicitud([], 'GET'),$incompatible->fresh()));
comprobar(str_contains($html,'pendiente de alta al recibir'),'La vista no distingue el material propuesto.');
// Probar los menús reales; solo se sustituye el aviso preexistente de producción.
$ultimoMaterial=(int) Material::max('id');
$codigoReservado='MAT-'.str_pad((string) ($ultimoMaterial+2),3,'0',STR_PAD_LEFT);
$reserva=Material::create(['nombre'=>'Reserva de código preexistente','unidad_medida'=>'unidad','codigo'=>$codigoReservado,'estado'=>true,'stock_actual'=>'7.00','stock_minimo'=>'1.00','costo_unitario'=>'2.00']);
$actor=$usuarios['Administrador']; $conReserva=datos(['detalles'=>[['tipo'=>'Material','material_id'=>'nuevo','material_nombre'=>'Nuevo con código ocupado','material_unidad'=>'unidad','descripcion'=>'Probar código sin colisión','cantidad'=>'1','precio'=>'4.00']]]);
$compraReserva=RegistroCompra::registrar($conReserva,$actor,'comprobantes/compras/nuevo.pdf'); RegistroCompra::recibir($compraReserva->id,1,'Verificado con código corto disponible.', $actor);
$nuevoReserva=Material::where('nombre','Nuevo con código ocupado')->firstOrFail();
comprobar($nuevoReserva->codigo!==$codigoReservado && (bool) preg_match('/^MAT-\d{3,}$/',$nuevoReserva->codigo) && $reserva->fresh()->codigo===$codigoReservado && $reserva->fresh()->stock_actual==='7.00','Un código ocupado se repite o modifica el material anterior.');
$codigoGuardado=$nuevoReserva->codigo; RegistroCompra::recibir($compraReserva->id,1,'Reintentar recepción sin cambiar código.',$actor);
comprobar($nuevoReserva->fresh()->codigo===$codigoGuardado && $nuevoReserva->fresh()->stock_actual==='1.00','Un reintento cambia el código o el stock.');
file_put_contents($temporal.'/fixture/layouts/app.blade.php',file_get_contents($raiz.'/resources/views/layouts/app.blade.php'));
mkdir($temporal.'/fixture/produccion/revisiones',0700,true);
file_put_contents($temporal.'/fixture/produccion/revisiones/aviso.blade.php','');
file_put_contents($temporal.'/fixture/contenido.blade.php',"@extends('layouts.app') @section('content')Prueba@endsection");
$rutaLayout=$app['view']->getFinder()->find('layouts.app'); clearstatcache(true,$rutaLayout);
$app['view.engine.resolver']->resolve('blade')->forgetCompiledOrNotExpired(); $app['blade.compiler']->compile($rutaLayout);
if (function_exists('opcache_invalidate')) opcache_invalidate($app['blade.compiler']->getCompiledPath($rutaLayout),true);
$app->instance(Illuminate\Foundation\Vite::class,new class extends Illuminate\Foundation\Vite { public function __invoke($entrypoints,$buildDirectory=null) { return new Illuminate\Support\HtmlString('<link rel="stylesheet" href="/tailwind.css">'); } });
foreach ($usuarios as $rol=>$usuario) {
    $actor=$usuario; solicitud([], 'GET'); $html=$app['view']->make('contenido')->render();
    comprobar(str_contains($html,'href="http://localhost/compras"')===in_array($rol,['Administrador','Contabilidad'],true),'El menú de Compras no corresponde al rol '.$rol);
    if (in_array($rol,['Administrador','Contabilidad'],true)) vista('layout-'.strtolower($rol),$controlador->index(solicitud([], 'GET')));
}
$migracionMaterial->down();
comprobar(!Schema::hasColumn('compra_detalles','material_solicitado') && Material::count()>=$cantidadMateriales,'La migración de solicitud altera materiales al revertir.');
$migracion->down(); comprobar(!Schema::hasTable('compras') && Schema::hasTable('materiales') && MovimientoInventario::count()>0,'La reversión elimina inventario o tablas previas.');
// Revisar gramática MySQL sin conexión, igual que las pruebas de Caja chica.
$app['config']->set('database.connections.mysql_solo_sql',['driver'=>'mysql','host'=>'sin-conexion.invalid','database'=>'prueba','username'=>'prueba','password'=>'','prefix'=>'','charset'=>'utf8mb4','collation'=>'utf8mb4_unicode_ci','version'=>'8.0.0']);
DB::setDefaultConnection('mysql_solo_sql'); DB::connection()->setPdo(new class extends PDO { public function __construct() {} public function getAttribute(int $atributo): mixed { return $atributo===PDO::ATTR_SERVER_VERSION ? '8.0.36' : null; } });
try {
    $sql=DB::connection()->pretend(fn () => $migracion->up()); comprobar(count($sql)>=4,'No se compiló MySQL.');
    foreach ($sql as $fila) comprobar((bool) preg_match('/^(create table|alter table) `(compras|compra_detalles|compra_correcciones|compra_historiales)`/',$fila['query']),'La migración modifica tablas existentes.');
    $sqlNuevo=DB::connection()->pretend(fn () => $migracionMaterial->up());
    comprobar(count($sqlNuevo)===1 && str_contains($sqlNuevo[0]['query'],'alter table `compra_detalles` add `material_solicitado` varchar(150) null'),'La nueva migración no se limita a la columna nullable.');
    if ($evidencias) file_put_contents($evidencias.'/migracion-mysql-sin-ejecutar.sql',implode(";\n",array_column($sql,'query')));
} finally { DB::setDefaultConnection('sqlite'); }
echo "OK: $cuenta comprobaciones de Compras. SQLite en memoria y archivos temporales; no se cargó .env.\n";
(new Illuminate\Filesystem\Filesystem)->deleteDirectory($temporal);
