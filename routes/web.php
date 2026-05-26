<?php



use App\Http\Controllers\TonerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SavendController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ReporteProximosMantenimientosController;
use App\Http\Controllers\SaprodController;
use App\Http\Controllers\SafactController;
use App\Http\Controllers\UserSucursalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Auth Route::get('signup', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');*/

Route::get('turno/sucursal/{sucursal?}', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');

Route::get('/panelclientes',  [App\Http\Controllers\SiteController::class, 'panelclientes']  )->name('panelclientes');
Route::get('/webhooks',  [App\Http\Controllers\SiteController::class, 'webhooks']  )->name('webhooks');

Route::match(['get','post'],'/',  [App\Http\Controllers\SiteController::class, 'index']  )->name('site');
Route::get('/unsubscribe',  [App\Http\Controllers\SiteController::class, 'index'] )->name('unsubscribe');
Route::get('/promociones',  [App\Http\Controllers\SiteController::class, 'promociones'] )->name('promociones');
Route::get( '/producto/{id}',    [App\Http\Controllers\SiteController::class, 'index'] )->name('ver.producto');
Route::get( '/promoid/{id}',     [App\Http\Controllers\SiteController::class, 'promoid'] )->name('ver.promoid');
Route::get( '/promocion/{id}',   [App\Http\Controllers\SiteController::class, 'promo'] )->name('ver.promo');
Route::get( '/busqueda/{id?}',   [App\Http\Controllers\SiteController::class, 'index'] )->name('url.busqueda');
Route::get( '/promociones/{busqueda?}',  [App\Http\Controllers\SiteController::class, 'promociones'] )->name('url.busquedapromo');
Route::post( '/gourlpromo',  [App\Http\Controllers\SiteController::class, 'gourlpromo'] )->name('gourlpromo');
Route::post( '/gourl',  [App\Http\Controllers\SiteController::class, 'gourl'] )->name('gourl');
Route::get( '/instancia/{id}',  [App\Http\Controllers\SiteController::class, 'index'] )->name('url.instancia');
Route::get( '/pagar/{amount}',  [App\Http\Controllers\SiteController::class, 'pago'] )->name('pagar.monto');
Route::put( '/procesar/pago',  [App\Http\Controllers\SiteController::class, 'procesar'] )->name('procesar.pago');
Route::put( '/encoded/msg',  [App\Http\Controllers\SiteController::class, 'encoded'] )->name('encode.msg');
//Route::get('/agregar/producto',  [App\Http\Controllers\ComprasController::class, 'agregar']  )->name('agregar.producto');
Route::get('/actualizar/agregados',  [App\Http\Controllers\SiteController::class, 'agregados'] )->name('actualizar.agregados');
//Route::get('/abrir/lista',  [App\Http\Controllers\ComprasController::class, 'abrirlista']  )->name('abrir.lista');
Route::post('/update/csrf',  [App\Http\Controllers\SiteController::class, 'tokencsrf'] )->name('update.csrf');



Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Auth::routes();

// Route::post('login', 'Auth\LoginController@login')->name('login');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('register', 'Auth\RegisterController@register')->name('register');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});


Route::middleware(['check.admin'])->group(function () {

    Route::prefix('usersucursal')->group(function () {
        Route::get('/', [UserSucursalController::class, 'index'])->name('usersucursal.index');
        Route::get('/usuarios', [UserSucursalController::class, 'getUsersConSucursales'])->name('usersucursal.usuarios');
        Route::get('/sucursales', [UserSucursalController::class, 'getAllSucursales'])->name('usersucursal.sucursales');
        Route::get('/sucursales-asignadas/{userId}', [UserSucursalController::class, 'getSucursalesAsignadasPorUsuario']);
        Route::get('/usuarios-por-sucursal/{sucursalId}', [UserSucursalController::class, 'getUsuariosPorSucursal']);
        Route::post('/asignar', [UserSucursalController::class, 'asignarSucursal'])->name('usersucursal.asignar');
        Route::post('/quitar', [UserSucursalController::class, 'quitarSucursal'])->name('usersucursal.quitar');
    });

    Route::post('/mantenimiento-rapido/listar-fotos',
        [App\Http\Controllers\MantenimientoRapidoController::class, 'listarFotosVehiculo'])
        ->name('mantenimiento.rapido.listar-fotos');

    Route::post('/mantenimiento-rapido/eliminar-foto-vehiculo',
        [App\Http\Controllers\MantenimientoRapidoController::class, 'eliminarFotoVehiculo'])
        ->name('mantenimiento.rapido.eliminar-foto-vehiculo');

    Route::post('/mantenimiento-rapido/actualizar-foto-vehiculo',
        [App\Http\Controllers\MantenimientoRapidoController::class, 'actualizarFotoVehiculo'])
        ->name('mantenimiento.rapido.actualizar-foto-vehiculo');

    Route::get('/mantenimiento-rapido', [App\Http\Controllers\MantenimientoRapidoController::class, 'index'])->name('mantenimiento.rapido');

    Route::post('/mantenimiento-rapido/buscar-vehiculo' , [App\Http\Controllers\MantenimientoRapidoController::class, 'buscarVehiculo']         )->name('mantenimiento.rapido.buscar');
    Route::post('/mantenimiento-rapido/guardar'         , [App\Http\Controllers\MantenimientoRapidoController::class, 'guardar']                )->name('mantenimiento.rapido.guardar');
    Route::post('/mantenimiento-rapido/crear-cliente'   , [App\Http\Controllers\MantenimientoRapidoController::class, 'crearCliente']           )->name('mantenimiento.rapido.crear-cliente');
    Route::post('/mantenimiento-rapido/crear-vehiculo'  , [App\Http\Controllers\MantenimientoRapidoController::class, 'crearVehiculo']          )->name('mantenimiento.rapido.crear-vehiculo');
    Route::post('/mantenimiento-rapido/obtener-vehiculo', [App\Http\Controllers\MantenimientoRapidoController::class, 'obtenerDetallesVehiculo'])->name('mantenimiento.rapido.obtener-vehiculo');
    Route::get ('/mantenimiento/comprobante/{id}'       , [App\Http\Controllers\CWMantenimientoController    ::class, 'comprobante']            )->name('mantenimiento.comprobante');
    Route::post('/mantenimiento-rapido/buscar-cliente'  , [App\Http\Controllers\MantenimientoRapidoController::class, 'buscarCliente']          )->name('mantenimiento.rapido.buscar-cliente');
    Route::post('/mantenimiento-rapido/buscar-productos', [App\Http\Controllers\MantenimientoRapidoController::class, 'buscarProductos']        )->name('mantenimiento.rapido.buscar-productos');

    Route::get('/mantenimientos/diario'                       , [App\Http\Controllers\MantenimientoDiarioController::class, 'index']       )->name('mantenimientos.diario');
    Route::get('/mantenimientos/diario/count'                 , [App\Http\Controllers\MantenimientoDiarioController::class, 'contadordia'] )->name('mantenimientos.diario.contadordia');
    Route::get('/mantenimientos/diario/detalle/{id}'          , [App\Http\Controllers\MantenimientoDiarioController::class, 'getDetalle']  )->name('mantenimientos.diario.detalle');
    Route::post('/mantenimientos/diario/{id}/actualizar-campo', [App\Http\Controllers\MantenimientoDiarioController::class, 'updateCampo'] )->name('mantenimientos.diario.update-campo');
    Route::post('/mantenimientos/diario/cambiar-fecha'        , [App\Http\Controllers\MantenimientoDiarioController::class, 'cambiarFecha'])->name('mantenimientos.diario.cambiar-fecha');


    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::put('/vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{id}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
    Route::get('/vehiculos/{id}/detalles', [VehiculoController::class, 'getDetalles'])->name('vehiculos.detalles');


    Route::get('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos',
        [\App\Http\Controllers\CWMantenimientoController::class, 'index'])->name('clientes.vehiculos.mantenimientos');

    Route::get('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos/create',
        [\App\Http\Controllers\CWMantenimientoController::class, 'create'])->name('clientes.vehiculos.mantenimientos.create');

    Route::post('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos',
        [\App\Http\Controllers\CWMantenimientoController::class, 'store'])->name('clientes.vehiculos.mantenimientos.store');

    Route::get('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos/{id}',
        [\App\Http\Controllers\CWMantenimientoController::class, 'show'])->name('clientes.vehiculos.mantenimientos.show');

    Route::get('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos/{id}/edit',
        [\App\Http\Controllers\CWMantenimientoController::class, 'edit'])->name('clientes.vehiculos.mantenimientos.edit');

    Route::post('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos/{id}/update',
        [\App\Http\Controllers\CWMantenimientoController::class, 'update'])->name('clientes.vehiculos.mantenimientos.update');

    Route::delete('/clientes/{codclie}/vehiculos/{vehiculo}/mantenimientos/{id}',
        [\App\Http\Controllers\CWMantenimientoController::class, 'destroy'])->name('clientes.vehiculos.mantenimientos.destroy');

    Route::post('/mantenimiento-rapido/actualizar-cliente',
        [App\Http\Controllers\MantenimientoRapidoController::class, 'actualizarCliente'])
        ->name('mantenimiento.rapido.actualizar-cliente');

    Route::post('/mantenimiento-rapido/actualizar-vehiculo',
        [App\Http\Controllers\MantenimientoRapidoController::class, 'actualizarVehiculo'])
        ->name('mantenimiento.rapido.actualizar-vehiculo');

    Route::resource('tokens', \App\Http\Controllers\CwtokenController::class);
    Route::controller(\App\Http\Controllers\CwtokenController::class)->group(function () {
        Route::match(['get','post'],'reporte/tokens', 'reportetokens')->name('reportetokens');
        Route::post('token/update', 'tokenupdate')->name('tokenupdate');
    });

    Route::get('/verpermisos/{id?}', [PermissionController::class, 'showForm'])->name('permissions.assign');
    Route::post('/verpermisos', [PermissionController::class, 'assign']);
    Route::post('/create/permissions', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/revoke/{user}/{permiso}', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');


    Route::get('/cambiarcomercial/{comercialid}', [App\Http\Controllers\HomeController::class, 'cambiarcomercial'])->name('cambiarcomercial');

    Route::post('saprod/update', [App\Http\Controllers\SaprodController::class, 'updateSaprodData']);
    Route::get('saprod/export/{codalte?}', [App\Http\Controllers\SaprodController::class, 'saprodexport']);

    Route::resource('vendedores', SavendController::class);
    Route::controller(SavendController::class)->group(function () {
        Route::get('savend/json', 'json')->name('vendedores.json');
    });

    Route::match(['get','post'],'/reporte/baterias',    [App\Http\Controllers\HomeController::class, 'reportebaterias'])   ->name('reportemotos');
    Route::match(['get','post'],'/reporte/lubricantes', [App\Http\Controllers\HomeController::class, 'reportelubricantes'])->name('reportelubricantes');
    Route::match(['get','post'],'/reporte/filtros',     [App\Http\Controllers\HomeController::class, 'reportefiltros'])    ->name('reporterepuestos');

    Route::match(['get','post'],'/resumenVentas', [App\Http\Controllers\HomeController::class, 'resumenVentas'])->name('resumenVentas');

    Route::resource('instancias', \App\Http\Controllers\SainstaController::class);
    Route::controller(\App\Http\Controllers\SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json')->name('sainsta.json');
        Route::post('sainsta/check/lastprod/{codinst}', 'lastprod');
    });


    Route::resource('proveedores', \App\Http\Controllers\SaprovController::class);
    Route::controller(\App\Http\Controllers\SaprovController::class)->group(function () {
        Route::get('saprov/json', 'json');
    });


    Route::resource('transferencias', \App\Http\Controllers\CwtransferenciasController::class);
    Route::controller(\App\Http\Controllers\CwtransferenciasController::class)->group(function () {

        Route::match(['get','post'],'reporte/transferencias', 'reportetransferencias')->name('reportetransferencias');
        Route::post('transferencias/json/{busquedatransf}/{status}/{fechas}', 'json');
        Route::get('transferencias/status/{status}', 'filtrarstatus');
        Route::post('transferencias/verificar', 'verificar');
        Route::match(['get','post'],'transferencia/informacion', 'informacion');
    });

    Route::prefix('productos')->group(function () {
        Route::get('/{id}/imagenes', [SaprodController::class, 'getProductoImagenes'])->name('productos.imagenes');
        Route::post('/{id}/imagen-principal', [SaprodController::class, 'subirImagenPrincipal'])->name('productos.imagen.principal');
        Route::post('/{id}/imagenes-adicionales', [SaprodController::class, 'subirImagenesAdicionales'])->name('productos.imagenes.subir');
        Route::delete('/{id}/imagen/{imagenId}', [SaprodController::class, 'eliminarImagen'])->name('productos.imagen.eliminar');
        Route::post('/{id}/reordenar-imagenes', [SaprodController::class, 'reordenarImagenes'])->name('productos.imagenes.reordenar');
    });

    Route::resource('productos', \App\Http\Controllers\SaprodController::class);
    Route::controller(\App\Http\Controllers\SaprodController::class)->group(function () {
        Route::get('saprod/json', 'json');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::post('saprod/upload', 'upload');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::match(['get','post'],'ventas/resultado', 'resultadosucursales');
        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
        Route::match(['get','post'],'/operaciones/{codprod?}', 'index');
        Route::match(['get','post'],'mermas/sucursales', 'mermassucursales');
        Route::get( '/existencia/lubricantes', 'existenciasLubricantes');
    });




    Route::get('/cliente/mantenimiento/{token}', [\App\Http\Controllers\ClienteMantenimientoController::class, 'verPorToken'])
        ->name('cliente.mantenimiento.ver');

    Route::post('/mantenimiento/notificar', [\App\Http\Controllers\MantenimientoRapidoController::class, 'notificarCliente'])
        ->name('mantenimiento.notificar');

// Rutas para autocompletado predictivo
    Route::get('/mantenimiento-rapido/marcas', [\App\Http\Controllers\MantenimientoRapidoController::class, 'obtenerMarcas'])->name('mantenimiento.rapido.marcas');
    Route::get('/mantenimiento-rapido/modelos', [\App\Http\Controllers\MantenimientoRapidoController::class, 'obtenerModelos'])->name('mantenimiento.rapido.modelos');

    Route::controller(\App\Http\Controllers\SasucursalController::class)->group(function () {
        Route::post('sascursal/bancos', 'bancos');
    });


    Route::post('/mantenimiento/{id}/generar-token', [\App\Http\Controllers\ClienteMantenimientoController::class, 'generarToken'])
        ->name('mantenimiento.generar-token');

    Route::post('/mantenimiento/{id}/enviar-whatsapp', [\App\Http\Controllers\ClienteMantenimientoController::class, 'enviarWhatsApp'])
        ->name('mantenimiento.enviar-whatsapp');


    Route::resource('depositos', \App\Http\Controllers\SadepoController::class);
    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::controller(\App\Http\Controllers\SaacxcController::class)->group(function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxc');
        Route::post('/cxclist', 'cxclist');
    });

    Route::resource('instpago', \App\Http\Controllers\SatarjController::class);
    Route::controller(\App\Http\Controllers\SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
    });

    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');

    Route::match(['get','post'],'/reporte/venta', [App\Http\Controllers\HomeController::class, 'reporteventa'])->name('reporteventa');
    Route::post('/reporte/venta/sucu', [App\Http\Controllers\HomeController::class, 'reporteventasucu'])->name('reporteventasucu');

    //Route::match(['get','post'],'/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::get('/reportes/proximos-mantenimientos', [ReporteProximosMantenimientosController::class, 'index'])
        ->name('reportes.proximos-mantenimientos');

    Route::post('/reportes/proximos-mantenimientos/{id}/contactar', [ReporteProximosMantenimientosController::class, 'marcarContactado'])
        ->name('reportes.proximos-mantenimientos.contactar');

    Route::post('/reportes/proximos-mantenimientos/{id}/confirmar', [ReporteProximosMantenimientosController::class, 'marcarConfirmacion'])
        ->name('reportes.proximos-mantenimientos.confirmar');

    Route::post('/reportes/proximos-mantenimientos/{id}/enviar-recordatorio', [ReporteProximosMantenimientosController::class, 'enviarRecordatorio'])
        ->name('reportes.proximos-mantenimientos.enviar-recordatorio');

});

Route::middleware(['auth'])->group(function () {


    Route::get('/buscarproducto/{codprod}/{comercial}', [\App\Http\Controllers\SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::controller(SafactController::class)->group(function () {
        Route::get('doc/{tipofac}/{numerod}/{fksucu}', 'documentoSafact')->name('facturaver');
        Route::post('openDoc', 'documentoAjax');
    });


    Route::controller(\App\Http\Controllers\SaclieController::class)->group(function () {
        Route::match(['get','post'],'/clientes/{codclie?}/{tab?}', 'index')->name('buscarclientes');
    });

    Route::match(['get','post'],'/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('logout',[\App\Http\Controllers\Auth\LoginController::class, 'logout']);

    //Route::resource('compraitems', \App\Http\Controllers\CompraItemsController::class);

    //Route::get('{any}', [TonerController::class, 'index']);
    Route::get('/bienvenido', [\App\Http\Controllers\SiteController::class, 'bienvenido'])->name('bienvenido');
    Route::get('components/{any}', [TonerController::class, 'components']);

});

Route::get('/m/{token}', [App\Http\Controllers\ClienteMantenimientoController::class, 'verMantenimiento'])
    ->name('cliente.mantenimiento.ver');

Route::get('/cliente/mantenimiento/{token}/confirmar', [\App\Http\Controllers\ClienteMantenimientoController::class, 'confirmarVista'])
    ->name('cliente.mantenimiento.confirmar-vista');

Route::post('/cliente/mantenimiento/procesar-confirmacion', [\App\Http\Controllers\ClienteMantenimientoController::class, 'procesarConfirmacion'])
    ->name('cliente.mantenimiento.procesar-confirmacion');
