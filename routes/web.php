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

Route::match(['get','post'],'/',  [App\Http\Controllers\SiteController::class, 'index']  )->name('site');
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Auth::routes();
 

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});


