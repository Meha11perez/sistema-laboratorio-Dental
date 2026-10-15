<?php
/** Pruebas aisladas: SQLite en memoria y archivos temporales; no carga .env ni la base del sistema. */
$raiz = dirname(__DIR__, 2);
$loader = require $raiz.'/vendor/autoload.php';
$loader->addPsr4('App\\', $raiz.'/app', true);

use App\Http\Controllers\CajaChicaController;
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

$temporal = sys_get_temp_dir().'/prueba-caja-chica-'.bin2hex(random_bytes(6));
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
$app['router']->get('/login', fn()=>null)->name('login');
$app['router']->post('/logout', fn()=>null)->name('logout');
$app['router']->getRoutes()->refreshNameLookups();

(require $raiz.'/database/migrations/0000_12_31_235959_create_roles_table.php')->up();
(require $raiz.'/database/migrations/0001_01_01_000000_create_users_table.php')->up();
Schema::table('users', fn($t) => $t->string('username')->nullable()->unique());
foreach (['ordenes_trabajo','pagos','abonos','clinicas','odontologos','pacientes','materiales'] as $tabla) {
    Schema::create($tabla, function($t) { $t->id(); $t->string('dato_original'); });
    DB::table($tabla)->insert(['dato_original'=>'REGISTRO ORIGINAL DE PRUEBA']);
}
$tablasAntes = Schema::getTableListing();
$migracion = require $raiz.'/database/migrations/2026_10_14_000002_crear_caja_chica.php';
$migracion->up();
$usuarios=[];
foreach (['Administrador','Contabilidad','Recepcion','Técnico','Mensajero'] as $nombre) {
    $rol=Role::create(['nombre'=>$nombre,'estado'=>true]);
    $usuarios[$nombre]=User::create(['role_id'=>$rol->id,'estado'=>true,'name'=>'Prueba '.$nombre,'email'=>'prueba'.$rol->id.'@example.test','password'=>'clave-solo-de-prueba']);
}
$actor=$usuarios['Contabilidad'];
$controlador=new CajaChicaController;
$cuenta=0;
function comprobar(bool $condicion, string $mensaje): void { global $cuenta; if (!$condicion) throw new RuntimeException($mensaje); $cuenta++; }
function rechazar(callable $fn, ?int $codigo=null): void {
    global $cuenta;
    try { $fn(); } catch (ValidationException $e) { if ($codigo !== null && $codigo !== 422) throw $e; $cuenta++; return; }
    catch (HttpException $e) { if ($codigo !== null && $e->getStatusCode() !== $codigo) throw $e; $cuenta++; return; }
    throw new RuntimeException('Se permitió una operación que debía rechazarse.');
}
function solicitud(array $datos=[], string $metodo='POST', array $archivos=[]): Request {
    global $app,$actor;
    $r=Request::create('http://localhost/caja-chica', $metodo, $datos, [], $archivos);
    $r->setLaravelSession($app['session']->driver());
    $r->setUserResolver(fn()=>$actor);
    if ($metodo === 'GET') $r->setRouteResolver(fn()=>$app['router']->getRoutes()->match($r));
    $app->instance('request',$r);
    $app->instance('auth',new Illuminate\Auth\RequestGuard(fn()=>$actor,$r));
    return $r;
}
function datos(array $cambios=[]): array {
    return array_merge(['clave_registro'=>(string) Str::uuid(),'fecha_gasto'=>'2026-10-15','concepto'=>'Compra de agua pura',
        'beneficiario'=>'Proveedor de prueba','responsable'=>'Responsable de prueba','cantidad'=>'1','precio_unitario'=>'25.50',
        'metodo_pago'=>'Efectivo','tipo_comprobante'=>'Recibo','numero_documento'=>'R-100','nit'=>null,'referencia'=>null,'observaciones'=>null], $cambios);
}
function gasto(array $cambios=[]): CajaChicaGasto { global $actor; return CajaChica::registrar(datos($cambios), $actor, null); }
function correccion(CajaChicaGasto $g, array $cambios=[]): CajaChicaCorreccion {
    global $actor;
    return CajaChica::solicitarCorreccion($g->id, datos(array_merge([
        'tipo'=>'Corregir','version_gasto'=>$g->fresh()->version,'motivo_solicitud'=>'Se revisó el documento original.',
        'fecha_gasto'=>$g->fecha_gasto->toDateString(),'concepto'=>$g->concepto,'beneficiario'=>$g->beneficiario,'responsable'=>$g->responsable,
        'cantidad'=>$g->cantidad,'precio_unitario'=>$g->precio_unitario,'metodo_pago'=>$g->metodo_pago,
        'tipo_comprobante'=>$g->tipo_comprobante,'numero_documento'=>$g->numero_documento,
        'nit'=>$g->nit,'referencia'=>$g->referencia,'observaciones'=>$g->observaciones,
    ], $cambios)), $actor, null);
}
function vista(string $nombre, $respuesta): string {
    global $evidencias;
    $html=$respuesta->render();
    comprobar(!str_contains($html, '<?php'), 'La vista contiene PHP sin compilar.');
    if ($evidencias) file_put_contents($evidencias.'/'.$nombre.'.html',$html);
    return $html;
}
$evidencias=getenv('CAJA_CHICA_EVIDENCIAS') ?: null;
if ($evidencias && !is_dir($evidencias)) mkdir($evidencias,0700,true);
solicitud();
comprobar(count(array_diff(Schema::getTableListing(),$tablasAntes))===3,'La migración no se limita a las tres tablas nuevas.');
comprobar(Importe::centavos('25.50')===2550 && Importe::centavos('0.10')+Importe::centavos('0.20')===30,'Error de centavos.');
comprobar(Importe::cantidadMilesimas('1.250')===1250 && Importe::cantidadDecimal(1250)==='1.25','Error de cantidades.');
comprobar(Importe::total(1005,100)===101 && Importe::total(1500,2550)===3825,'Error de redondeo a centavos.');
foreach (['1e3','-1','1,25','0.001','abc'] as $valor) {
    try { Importe::centavos($valor); throw new RuntimeException('Se aceptó importe inválido'); } catch (InvalidArgumentException) { $cuenta++; }
}
try { Importe::total(999999999,999999999999); throw new RuntimeException('Se aceptó desbordamiento.'); } catch (InvalidArgumentException) { $cuenta++; }
comprobar(Importe::total(1000,999999999999)===999999999999, 'El importe máximo no se calcula exactamente.');
$resumen=CajaChica::resumen('2026-10-01');
comprobar($resumen['limite']===500000 && $resumen['gastado']===0 && CajaChicaPeriodo::count()===0,'Consultar un mes vacío escribe registros.');

// Pruebas del controlador: validación, archivos y permisos de cada rol.
foreach (['Recepcion','Técnico','Mensajero'] as $rol) {
    $actor=$usuarios[$rol];
    rechazar(fn()=>$controlador->index(solicitud([], 'GET')),403);
    rechazar(fn()=>$controlador->store(solicitud(datos())),403);
    rechazar(fn()=>CajaChica::registrar(datos(),$actor,null),403);
}
$actor=$usuarios['Contabilidad'];
foreach ([['cantidad'=>'0'],['precio_unitario'=>'0'],['precio_unitario'=>'1,25'],['precio_unitario'=>'1e2'],['cantidad'=>'0.001','precio_unitario'=>'0.01'],
    ['fecha_gasto'=>'2026-10-16'],['responsable'=>''],['metodo_pago'=>'Cheque','referencia'=>null],['metodo_pago'=>'Transferencia','referencia'=>null],
    ['cantidad'=>'999999.999','precio_unitario'=>'9999999999.99']] as $cambio) {
    rechazar(fn()=>$controlador->store(solicitud(datos($cambio))),422);
}
comprobar(CajaChicaGasto::count()===0,'Una validación fallida dejó gastos.');
$archivo=UploadedFile::fake()->createWithContent('recibo.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF");
$d=datos(['cantidad'=>'2','precio_unitario'=>'25.50','monto'=>'0.01','registrado_por'=>$usuarios['Administrador']->id,'estado'=>'Autorizado']);
$controlador->store(solicitud($d,'POST',['comprobante'=>$archivo]));
$g=CajaChicaGasto::latest('id')->first();
comprobar($g->monto_centavos===5100 && $g->registrado_por===$actor->id && $g->estado==='Registrado','Se aceptó monto, actor o estado enviado por el navegador.');
comprobar($g->responsable==='Responsable de prueba' && $g->numero_documento==='R-100','Se perdieron los campos del formato.');
comprobar(Storage::disk('local')->exists($g->comprobante_path),'No guardó el respaldo privado.');
$descarga=$controlador->comprobante(solicitud([], 'GET'),$g);
comprobar(str_contains($descarga->headers->get('Cache-Control'),'private') && $descarga->headers->get('X-Content-Type-Options')==='nosniff','Descarga sin cabeceras privadas.');
$antesArchivos=count(Storage::disk('local')->allFiles());
$controlador->store(solicitud($d,'POST',['comprobante'=>UploadedFile::fake()->createWithContent('recibo.pdf',"%PDF-1.4\n%%EOF")]));
comprobar(CajaChicaGasto::count()===1 && count(Storage::disk('local')->allFiles())===$antesArchivos,'Doble envío duplicó gasto o comprobante.');
$mal=UploadedFile::fake()->createWithContent('respaldo.php','<?php echo "no";');
rechazar(fn()=>$controlador->store(solicitud(datos(),'POST',['comprobante'=>$mal])),422);
comprobar(count(Storage::disk('local')->allFiles())===$antesArchivos,'Un archivo rechazado quedó almacenado.');
$usuarioInactivo=$actor->replicate();$usuarioInactivo->estado=false;
rechazar(fn()=>CajaChica::acceso($usuarioInactivo),403);
$rolActivo=$actor->role;$rolActivo->estado=false;rechazar(fn()=>CajaChica::acceso($actor),403);$rolActivo->estado=true;

// Límite exacto y excedente: aprobar no aumenta el presupuesto de gastos posteriores.
$limite=gasto(['precio_unitario'=>'4949.00']);
comprobar(CajaChica::resumen('2026-10-01')['gastado']===500000 && $limite->estado==='Registrado','Q5,000 exactos se rechazaron.');
$pendiente=gasto(['precio_unitario'=>'0.01','metodo_pago'=>'Cheque','referencia'=>'CH-01']);
comprobar($pendiente->estado==='Pendiente de autorización' && CajaChica::resumen('2026-10-01')['gastado']===500000,'Un excedente se sumó antes de aprobar.');
rechazar(fn()=>$controlador->resolver(solicitud(['decision'=>'aprobar','motivo'=>'Intento contable']),$pendiente),403);
$actor=$usuarios['Administrador'];
rechazar(fn()=>CajaChica::resolverExcedente($pendiente->id,'aprobar','',$actor),422);
CajaChica::resolverExcedente($pendiente->id,'aprobar','Compra de emergencia autorizada.',$actor);
comprobar($pendiente->fresh()->estado==='Autorizado' && $pendiente->fresh()->autorizado_por===$actor->id && CajaChica::resumen('2026-10-01')['gastado']===500001,'La autorización no registra monto y actor.');
rechazar(fn()=>CajaChica::resolverExcedente($pendiente->id,'aprobar','Duplicado',$actor),422);
$posterior=gasto(['precio_unitario'=>'1.00']);
comprobar($posterior->estado==='Pendiente de autorización','La aprobación previa amplió el límite.');
CajaChica::resolverExcedente($posterior->id,'rechazar','No procede.',$actor);
comprobar($posterior->fresh()->estado==='Rechazado' && CajaChica::resumen('2026-10-01')['gastado']===500001,'El gasto rechazado altera totales.');
rechazar(fn()=>correccion($posterior,['precio_unitario'=>'2.00']),422);

// Correcciones: cambios en espera, rechazo, aprobación, historial y cambio de mes.
$actor=$usuarios['Contabilidad'];
rechazar(fn()=>correccion($g),422);
$c=correccion($g,['precio_unitario'=>'30.00','responsable'=>'Responsable corregido','numero_documento'=>'R-101']);
comprobar($g->fresh()->monto_centavos===5100 && $c->datos_anteriores['responsable']==='Responsable de prueba','La propuesta modificó el gasto antes de aprobar.');
rechazar(fn()=>correccion($g,['precio_unitario'=>'31.00']),422);
rechazar(fn()=>CajaChica::resolverCorreccion($c->id,'aprobar','No autorizado',$actor),403);
$actor=$usuarios['Administrador'];
CajaChica::resolverCorreccion($c->id,'rechazar','La boleta original es correcta.',$actor);
comprobar($g->fresh()->monto_centavos===5100 && $c->fresh()->datos_resultantes===null && $c->fresh()->estado==='Rechazada','El rechazo aplicó la corrección.');
$c2=correccion($g,['precio_unitario'=>'30.00','responsable'=>'Responsable corregido','numero_documento'=>'R-101']);
CajaChica::resolverCorreccion($c2->id,'aprobar','Verificado con el documento.',$actor);
comprobar($g->fresh()->monto_centavos===6000 && $g->fresh()->responsable==='Responsable corregido' && $g->fresh()->estado==='Autorizado','No aplicó o no autorizó la corrección excedente.');
comprobar($c2->fresh()->datos_anteriores['monto_centavos']===5100 && $c2->fresh()->datos_resultantes['monto_centavos']===6000 && $c2->fresh()->resuelto_por===$actor->id,'Historial anterior y aplicado incorrectos.');
rechazar(fn()=>CajaChica::resolverCorreccion($c2->id,'aprobar','Doble aprobación',$actor),422);
$cuentaAntes=CajaChica::resumen('2026-10-01')['gastado'];
$c3=correccion($g->fresh(),['fecha_gasto'=>'2026-09-15','cantidad'=>'1.5','precio_unitario'=>'12.50']);
comprobar(CajaChica::resumen('2026-09-01')['gastado']===0,'La solicitud retroactiva altera el mes anterior.');
CajaChica::resolverCorreccion($c3->id,'aprobar','El recibo corresponde al mes anterior.',$actor);
comprobar(CajaChica::resumen('2026-10-01')['gastado']===$cuentaAntes-6000 && CajaChica::resumen('2026-09-01')['gastado']===1875,'Trasladar de mes no recalcula ambos totales.');
comprobar($g->fresh()->cantidad==='1.5' && $g->fresh()->precio_unitario==='12.50','Cantidad y precio no se conservaron.');
$actor=$usuarios['Contabilidad'];
rechazar(fn()=>CajaChica::solicitarCorreccion($g->id,datos(['tipo'=>'Anular','version_gasto'=>1,'motivo_solicitud'=>'Formulario antiguo']),$actor,null),422);
$anulacion=correccion($g->fresh(),['tipo'=>'Anular']);
comprobar(CajaChica::resumen('2026-09-01')['gastado']===1875,'Pedir anulación descontó antes de la aprobación.');
$actor=$usuarios['Administrador'];
CajaChica::resolverCorreccion($anulacion->id,'aprobar','Documento anulado.',$actor);
comprobar($g->fresh()->estado==='Anulado' && CajaChica::resumen('2026-09-01')['gastado']===0 && Storage::disk('local')->exists($g->comprobante_path),'Anular borró evidencia o mantuvo el monto en totales.');
rechazar(fn()=>correccion($g->fresh(),['concepto'=>'Cambio prohibido']),422);

// Una corrección pendiente bloquea autorizar el valor original.
$sinAprobar=gasto(['precio_unitario'=>'60.00']);
$peticion=correccion($sinAprobar,['precio_unitario'=>'65.00']);
rechazar(fn()=>CajaChica::resolverExcedente($sinAprobar->id,'aprobar','Primero revisar',$actor),422);
CajaChica::resolverCorreccion($peticion->id,'aprobar','Se autoriza el importe corregido.',$actor);
comprobar($sinAprobar->fresh()->estado==='Autorizado' && $sinAprobar->fresh()->monto_centavos===6500,'No se resolvió la corrección del excedente.');

// Comprobante reemplazado: conservar evidencia anterior y no dejar archivos de solicitudes fallidas.
$actor=$usuarios['Contabilidad'];
$gDocumento=gasto(['fecha_gasto'=>'2026-07-10','precio_unitario'=>'15.00']);
$datosDoc=datos(['fecha_gasto'=>'2026-07-10','tipo'=>'Corregir','version_gasto'=>$gDocumento->fresh()->version,
    'motivo_solicitud'=>'Se adjunta el recibo que faltaba.','precio_unitario'=>'15.00']);
$controlador->guardarCorreccion(solicitud($datosDoc,'POST',['comprobante'=>UploadedFile::fake()->createWithContent('nuevo.pdf',"%PDF-1.4\n%%EOF")]),$gDocumento);
$cDoc=$gDocumento->correcciones()->latest('id')->first();
comprobar($gDocumento->fresh()->comprobante_path===null && Storage::disk('local')->exists($cDoc->datos_propuestos['comprobante_path']),'Un respaldo propuesto se aplicó antes de aprobar.');
$totalArchivos=count(Storage::disk('local')->allFiles());
$controlador->guardarCorreccion(solicitud($datosDoc,'POST',['comprobante'=>UploadedFile::fake()->createWithContent('nuevo.pdf',"%PDF-1.4\n%%EOF")]),$gDocumento);
comprobar($gDocumento->correcciones()->count()===1 && count(Storage::disk('local')->allFiles())===$totalArchivos,'La solicitud duplicada dejó una copia del archivo.');
$actor=$usuarios['Administrador'];
$controlador->resolverCorreccion(solicitud(['decision'=>'aprobar','motivo'=>'Recibo revisado.']),$cDoc);
comprobar($gDocumento->fresh()->comprobante_path===$cDoc->datos_propuestos['comprobante_path'],'El comprobante autorizado no se aplicó.');
$descarga=$controlador->comprobanteCorreccion(solicitud([], 'GET'),$cDoc->fresh());
comprobar($descarga->getStatusCode()===200,'No permite descargar la evidencia de la corrección.');
$actor=$usuarios['Contabilidad'];
$datosViejos=array_merge($datosDoc,['clave_registro'=>(string)Str::uuid(),'precio_unitario'=>'16.00']);
rechazar(fn()=>$controlador->guardarCorreccion(solicitud($datosViejos,'POST',['comprobante'=>UploadedFile::fake()->createWithContent('viejo.pdf',"%PDF-1.4\n%%EOF")]),$gDocumento),422);
comprobar(count(Storage::disk('local')->allFiles())===$totalArchivos,'Un formulario obsoleto dejó archivos sin registrar.');
rechazar(fn()=>CajaChica::registrar($d,$usuarios['Administrador'],null),403);

// Rutas reales: nombres únicos, orden de rutas estáticas y restricciones del servidor.
$rutas=$app['router']->getRoutes();$nombres=[];
foreach ($rutas as $ruta) { if ($ruta->getName()) { comprobar(!isset($nombres[$ruta->getName()]),'Hay nombres de rutas duplicados.'); $nombres[$ruta->getName()]=true; } }
foreach ([['/caja-chica','GET','index'],['/caja-chica/crear','GET','create'],['/caja-chica/solicitudes','GET','solicitudes'],['/caja-chica/imprimir','GET','imprimir'],['/caja-chica/exportar','GET','exportar']] as [$uri,$method,$nombre]) {
    $ruta=$rutas->match(Request::create($uri,$method));
    comprobar($ruta->getName()==='caja-chica.'.$nombre && in_array('auth',$ruta->gatherMiddleware()) && in_array('role:Administrador,Contabilidad',$ruta->gatherMiddleware()),'Ruta estática sin autenticación o rol correcto.');
}
foreach ([['/caja-chica/1/autorizar','PATCH'],['/caja-chica/correcciones/1','PATCH']] as [$uri,$method]) comprobar(in_array('role:Administrador',$rutas->match(Request::create($uri,$method))->gatherMiddleware()),'Resolver no exige Administrador.');
foreach (['Recepcion','Técnico','Mensajero'] as $rol) {
    $actor=$usuarios[$rol];
    rechazar(fn()=>$controlador->show(solicitud([], 'GET'),$g->fresh()),403);
    rechazar(fn()=>$controlador->comprobante(solicitud([], 'GET'),$g->fresh()),403);
    rechazar(fn()=>$controlador->exportar(solicitud([], 'GET')),403);
    rechazar(fn()=>$controlador->guardarCorreccion(solicitud(['tipo'=>'Anular','clave_registro'=>(string)Str::uuid(),'version_gasto'=>$g->version,'motivo_solicitud'=>'Intento']),$g),403);
}

// Render de vistas reales y reporte: escape de texto, selección de meses y exportación.
$actor=$usuarios['Contabilidad'];
$prueba=gasto(['fecha_gasto'=>'2026-08-12','numero_documento'=>'0000123','nit'=>'00123-K','referencia'=>'0000999','concepto'=>'=HYPERLINK("https://example.test")','beneficiario'=>'<script>alert(1)</script>','responsable'=>'Responsable de prueba','cantidad'=>'2','precio_unitario'=>'18.75']);
solicitud(['mes'=>'2026-08'],'GET');
$html=vista('index',$controlador->index(solicitud(['mes'=>'2026-08'],'GET')));
comprobar(str_contains($html,'37.50') && !str_contains($html,'<script>alert(1)</script>'),'La vista no escapa texto o pierde totales.');
vista('create',$controlador->create(solicitud([], 'GET')));
vista('corregir',$controlador->solicitarCorreccion(solicitud([], 'GET'),$prueba));
$html=vista('show-contabilidad',$controlador->show(solicitud([], 'GET'),$sinAprobar->fresh()));
comprobar(!str_contains($html,'name="decision"'),'Contabilidad ve botones para aprobar.');
$actor=$usuarios['Administrador'];
vista('show-historial',$controlador->show(solicitud([], 'GET'),$g->fresh()));
$pendienteUi=gasto(['precio_unitario'=>'35.00']);
vista('show-pendiente',$controlador->show(solicitud([], 'GET'),$pendienteUi));
$corUi=correccion($prueba,['precio_unitario'=>'20.50']);
vista('show-correccion',$controlador->show(solicitud([], 'GET'),$prueba->fresh()));
vista('solicitudes',$controlador->solicitudes(solicitud([], 'GET')));
vista('imprimir',$controlador->imprimir(solicitud(['mes'=>'2026-08'],'GET')));
ob_start();$controlador->exportar(solicitud(['mes'=>'2026-08'],'GET'))->sendContent();$csv=ob_get_clean();
comprobar(str_contains($csv,"'=HYPERLINK") && str_contains($csv,'Precio unitario Q') && str_contains($csv,'37.50'),'CSV sin total correcto o sin protección de fórmulas.');
$filas=array_map(fn($fila)=>str_getcsv($fila,';','"',''),explode("\n",trim(substr($csv,3))));
comprobar(count($filas[5])===15 && count($filas[6])===15,'El CSV tiene columnas descuadradas.');
$lista=$controlador->index(solicitud(['mes'=>'2026-08','estado'=>'Anulado'],'GET'))->getData();
comprobar($lista['gastos']->total()===0 && $lista['resumen']['gastado']===3750,'Filtrar el listado altera el resumen mensual.');
rechazar(fn()=>$controlador->index(solicitud(['mes'=>'2026-99'],'GET')),422);
rechazar(fn()=>$controlador->index(solicitud(['estado'=>'Inventado'],'GET')),422);
$gMal=$prueba->replicate();$gMal->comprobante_path='../.env';rechazar(fn()=>$controlador->comprobante(solicitud([], 'GET'),$gMal),404);
$gMal->comprobante_path='comprobantes/caja-chica/no-existe.pdf';rechazar(fn()=>$controlador->comprobante(solicitud([], 'GET'),$gMal),404);

// Exportación Excel: archivo real, lectura sin modificar datos y permisos del servidor.
$estadoAntesExcel=[CajaChicaGasto::get()->toArray(),CajaChicaCorreccion::get()->toArray(),CajaChicaPeriodo::get()->toArray()];
$temporalesAntes=glob(sys_get_temp_dir().'/caja_excel_*');
foreach (['2026-08','2026-09','2026-10','2026-06'] as $mesExcel) {
    $respuestaExcel=$controlador->excel(solicitud(['mes'=>$mesExcel],'GET'));
    comprobar($respuestaExcel->getStatusCode()===200 && str_starts_with($respuestaExcel->getContent(),'PK'),'Excel no es un archivo XLSX válido.');
    comprobar(str_contains($respuestaExcel->headers->get('Content-Type'),'spreadsheetml.sheet') && str_contains($respuestaExcel->headers->get('Cache-Control'),'private'),'Descarga Excel sin tipo o privacidad correctos.');
    if ($evidencias) file_put_contents($evidencias.'/caja-'.$mesExcel.'.xlsx',$respuestaExcel->getContent());
}
comprobar(glob(sys_get_temp_dir().'/caja_excel_*')===$temporalesAntes,'Quedaron archivos temporales del reporte Excel.');
comprobar($estadoAntesExcel===[CajaChicaGasto::get()->toArray(),CajaChicaCorreccion::get()->toArray(),CajaChicaPeriodo::get()->toArray()],'Exportar Excel cambió registros del módulo.');
$rutaExcel=$rutas->match(Request::create('/caja-chica/excel','GET'));
comprobar($rutaExcel->getName()==='caja-chica.excel' && in_array('auth',$rutaExcel->gatherMiddleware()) && in_array('role:Administrador,Contabilidad',$rutaExcel->gatherMiddleware()),'La descarga Excel no conserva autenticación y roles.');
foreach (['Recepcion','Técnico','Mensajero'] as $rolExcel) {
    $actor=$usuarios[$rolExcel];rechazar(fn()=>$controlador->excel(solicitud(['mes'=>'2026-08'],'GET')),403);
}
$actor=$usuarios['Administrador'];
ob_start();$controlador->exportar(solicitud(['mes'=>'2026-08'],'GET'))->sendContent();$csvDespuesExcel=ob_get_clean();
comprobar($csvDespuesExcel===$csv,'La nueva descarga alteró el contenido del CSV.');

foreach (['ordenes_trabajo','pagos','abonos','clinicas','odontologos','pacientes','materiales'] as $tabla) comprobar(DB::table($tabla)->get()->toArray()[0]->dato_original==='REGISTRO ORIGINAL DE PRUEBA' && DB::table($tabla)->count()===1,'Se modificó la tabla existente '.$tabla);
// Integración con el menú real; se omite únicamente el aviso preexistente de producción.
file_put_contents($temporal.'/fixture/layouts/app.blade.php',file_get_contents($raiz.'/resources/views/layouts/app.blade.php'));
mkdir($temporal.'/fixture/produccion/revisiones',0700,true);
file_put_contents($temporal.'/fixture/produccion/revisiones/aviso.blade.php','');
file_put_contents($temporal.'/fixture/contenido.blade.php',"@extends('layouts.app') @section('content')Contenido de prueba@endsection");
// Usar la misma ruta resuelta por Laravel, también cuando Windows normaliza sus separadores.
$rutaLayoutPrueba = $app['view']->getFinder()->find('layouts.app');
clearstatcache(true, $rutaLayoutPrueba);
$app['view.engine.resolver']->resolve('blade')->forgetCompiledOrNotExpired();
$app['blade.compiler']->compile($rutaLayoutPrueba);
if (function_exists('opcache_invalidate')) {
    opcache_invalidate($app['blade.compiler']->getCompiledPath($rutaLayoutPrueba), true);
}
$app->instance(Illuminate\Foundation\Vite::class,new class extends Illuminate\Foundation\Vite {
    public function __invoke($entrypoints,$buildDirectory=null) { return new Illuminate\Support\HtmlString('<link rel="stylesheet" href="/tailwind.css">'); }
});
foreach ($usuarios as $rol=>$usuario) {
    $actor=$usuario;solicitud([], 'GET');
    $html=$app['view']->make('contenido')->render();
    comprobar(str_contains($html,'href="http://localhost/caja-chica"')===in_array($rol,['Administrador','Contabilidad'],true),'El menú de Caja chica no corresponde al rol '.$rol);
    if ($evidencias && in_array($rol,['Administrador','Contabilidad'],true)) {
        $vista=$controlador->index(solicitud(['mes'=>'2026-08'],'GET'));
        vista('layout-'.strtolower($rol),$vista);
    }
}
// Verificar la reversión únicamente en esta base descartable.
$migracion->down();
comprobar(!Schema::hasTable('caja_chica_gastos') && Schema::hasTable('users') && DB::table('pagos')->count()===1,'La reversión afecta tablas ajenas.');
// Compilar las instrucciones MySQL sin conexión ni ejecución de SQL.
$app['config']->set('database.connections.mysql_solo_sql',[
    'driver'=>'mysql','host'=>'prueba-sin-conexion.invalid','database'=>'prueba_solo_sql','username'=>'prueba',
    'password'=>'','prefix'=>'','charset'=>'utf8mb4','collation'=>'utf8mb4_unicode_ci','version'=>'8.0.0',
]);
DB::setDefaultConnection('mysql_solo_sql');
// PDO sin constructor ni conexión: solo informa una versión para compilar la gramática.
DB::connection()->setPdo(new class extends PDO {
    public function __construct() {}
    public function getAttribute(int $attribute): mixed { return $attribute === PDO::ATTR_SERVER_VERSION ? '8.0.36' : null; }
});
try {
    $instrucciones=DB::connection()->pretend(fn()=>$migracion->up());
    comprobar(count($instrucciones)>=3,'No se generó el SQL de MySQL.');
    foreach ($instrucciones as $instruccion) comprobar((bool)preg_match('/^(create table|alter table) `caja_chica_(periodos|gastos|correcciones)`/',$instruccion['query']),'La migración genera SQL sobre tablas ajenas.');
    if ($evidencias) file_put_contents($evidencias.'/migracion-mysql-sin-ejecutar.sql',implode(";\n",array_column($instrucciones,'query')).";\n");
} finally { DB::setDefaultConnection('sqlite'); }
echo "OK: $cuenta comprobaciones; SQLite en memoria y archivos temporales. No se cargó .env.\n";
if ($evidencias) echo "Vistas renderizadas en $evidencias\n";
(new Illuminate\Filesystem\Filesystem)->deleteDirectory($temporal);
