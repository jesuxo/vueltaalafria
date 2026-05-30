<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserSucursalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TeamPanelController;

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

Route::match(['get','post'],'/',  [App\Http\Controllers\SiteController::class, 'index'])->name('site');
Route::get('/login', function () {
    return view('auth.login');
});

Auth::routes();
Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});

// ============================================
// RUTAS PÚBLICAS - Vuelta a la Fría
// ============================================

// Home
Route::get('/home', [App\Http\Controllers\SiteController::class, 'index'])->name('home');
Route::get('/inicio', [App\Http\Controllers\SiteController::class, 'index'])->name('inicio');

// Información pública
Route::get('/informacion', [App\Http\Controllers\HomeController::class, 'info'])->name('info');
Route::get('/etapas', [App\Http\Controllers\HomeController::class, 'stages'])->name('public.stages');
Route::get('/resultados', [App\Http\Controllers\HomeController::class, 'results'])->name('public.results');
Route::get('/galeria', [App\Http\Controllers\HomeController::class, 'gallery'])->name('public.gallery');

// Inscripciones públicas
Route::get('/inscripcion/individual', [RegistrationController::class, 'individualForm'])->name('registration.individual.form');
Route::post('/inscripcion/individual', [RegistrationController::class, 'individualSubmit'])->name('registration.individual.submit');
Route::get('/inscripcion/verificar', [RegistrationController::class, 'checkStatus'])->name('registration.check');
Route::post('/inscripcion/verificar', [RegistrationController::class, 'checkStatus'])->name('registration.check.submit');

// ============================================
// PANEL DE EQUIPOS (Acceso con código)
// ============================================
Route::prefix('equipo')->name('team.')->group(function () {
    Route::get('/login', [TeamPanelController::class, 'loginForm'])->name('login');
    Route::post('/login', [TeamPanelController::class, 'login'])->name('login.submit');
    Route::post('/logout', [TeamPanelController::class, 'logout'])->name('logout');

    Route::middleware('team.auth')->group(function () {
        Route::get('/dashboard', [TeamPanelController::class, 'dashboard'])->name('dashboard');

        // Personal del equipo (Staff)
        Route::get('/staff', [TeamPanelController::class, 'staffIndex'])->name('staff');
        Route::post('/staff', [TeamPanelController::class, 'staffStore'])->name('staff.store');
        Route::delete('/staff/{id}', [TeamPanelController::class, 'staffDestroy'])->name('staff.destroy');

        // Vehículos
        Route::get('/vehiculos', [TeamPanelController::class, 'vehiclesIndex'])->name('vehicles');
        Route::post('/vehiculos', [TeamPanelController::class, 'vehiclesStore'])->name('vehicles.store');
        Route::delete('/vehiculos/{id}', [TeamPanelController::class, 'vehiclesDestroy'])->name('vehicles.destroy');

        // Fotos
        Route::get('/fotos', [TeamPanelController::class, 'photosIndex'])->name('photos');
        Route::post('/fotos', [TeamPanelController::class, 'photosStore'])->name('photos.store');
        Route::delete('/fotos/{id}', [TeamPanelController::class, 'photosDestroy'])->name('photos.destroy');

        // Inscripción del equipo
        Route::get('/inscripcion', [TeamPanelController::class, 'registrationForm'])->name('registration');
        Route::post('/inscripcion', [TeamPanelController::class, 'registrationSubmit'])->name('registration.submit');

        // Importar atletas desde Excel
        Route::get('/descargar-plantilla', [TeamPanelController::class, 'downloadTemplate'])->name('download.template');
        Route::post('/importar-atletas', [TeamPanelController::class, 'importAthletes'])->name('import.athletes');

        // Ver atletas del equipo
        Route::get('/atletas', [TeamPanelController::class, 'athletesIndex'])->name('athletes');
    });
});

// ============================================
// PANEL ADMINISTRATIVO
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard principal
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ========== GESTIÓN DE EQUIPOS ==========
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{id}/toggle-active', [TeamController::class, 'toggleActive'])->name('teams.toggle-active');
    Route::post('/teams/{id}/regenerate-code', [TeamController::class, 'regenerateAccessCode'])->name('teams.regenerate-code');

    // ========== GESTIÓN DE ATLETAS ==========
    Route::resource('athletes', AthleteController::class);
    Route::get('/athletes/classification/general', [AthleteController::class, 'generalClassification'])->name('athletes.classification');
    Route::get('/athletes/export', [AthleteController::class, 'export'])->name('athletes.export');
    Route::post('/athletes/bulk-import', [AthleteController::class, 'bulkImport'])->name('athletes.bulk-import');

    // ========== GESTIÓN DE ETAPAS ==========
    Route::resource('stages', StageController::class);

    // Horarios de etapas
    Route::get('/stages/{stageId}/schedules', [StageController::class, 'schedules'])->name('stages.schedules');
    Route::post('/stages/{stageId}/schedules', [StageController::class, 'addSchedule'])->name('stages.add-schedule');
    Route::put('/schedules/{scheduleId}', [StageController::class, 'updateSchedule'])->name('stages.update-schedule');
    Route::delete('/schedules/{scheduleId}', [StageController::class, 'deleteSchedule'])->name('stages.delete-schedule');

    // Resultados de etapas
    Route::get('/stages/{stageId}/results', [StageController::class, 'enterResults'])->name('stages.enter-results');
    Route::post('/stages/{stageId}/results', [StageController::class, 'saveResults'])->name('stages.save-results');
    Route::get('/stages/{stageId}/results/export', [StageController::class, 'exportResults'])->name('stages.export-results');

    // ========== RESULTADOS Y CLASIFICACIONES ==========
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/standings', [ResultController::class, 'standings'])->name('standings');
        Route::get('/points', [ResultController::class, 'pointsClassification'])->name('points');
        Route::get('/sprint', [ResultController::class, 'sprintClassification'])->name('sprint');
        Route::get('/mountain', [ResultController::class, 'mountainClassification'])->name('mountain');
        Route::get('/team', [ResultController::class, 'teamClassification'])->name('team');
        Route::get('/export', [ResultController::class, 'export'])->name('export');
        Route::get('/live', [ResultController::class, 'liveResults'])->name('live');
    });

    // ========== GESTIÓN DE INSCRIPCIONES ==========
    Route::prefix('registrations')->name('registrations.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'registrations'])->name('index');
        Route::get('/pending', [AdminDashboardController::class, 'pendingRegistrations'])->name('pending');
        Route::get('/approved', [AdminDashboardController::class, 'approvedRegistrations'])->name('approved');
        Route::get('/rejected', [AdminDashboardController::class, 'rejectedRegistrations'])->name('rejected');
        Route::get('/paid', [AdminDashboardController::class, 'paidRegistrations'])->name('paid');
        Route::post('/{id}/approve', [AdminDashboardController::class, 'approveRegistration'])->name('approve');
        Route::post('/{id}/reject', [AdminDashboardController::class, 'rejectRegistration'])->name('reject');
        Route::post('/{id}/mark-paid', [AdminDashboardController::class, 'markAsPaid'])->name('mark-paid');
        Route::delete('/{id}', [AdminDashboardController::class, 'deleteRegistration'])->name('delete');
        Route::get('/export', [AdminDashboardController::class, 'exportRegistrations'])->name('export');
    });

    // ========== GESTIÓN DE FOTOS ==========
    Route::prefix('photos')->name('photos.')->group(function () {
        Route::get('/pending', [AdminDashboardController::class, 'pendingPhotos'])->name('pending');
        Route::get('/approved', [AdminDashboardController::class, 'approvedPhotos'])->name('approved');
        Route::post('/{id}/approve', [AdminDashboardController::class, 'approvePhoto'])->name('approve');
        Route::post('/{id}/reject', [AdminDashboardController::class, 'rejectPhoto'])->name('reject');
        Route::delete('/{id}', [AdminDashboardController::class, 'deletePhoto'])->name('delete');
        Route::get('/gallery', [AdminDashboardController::class, 'gallery'])->name('gallery');
    });

    // ========== REPORTES ==========
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'reports'])->name('index');
        Route::get('/teams', [AdminDashboardController::class, 'teamsReport'])->name('teams');
        Route::get('/athletes', [AdminDashboardController::class, 'athletesReport'])->name('athletes');
        Route::get('/financial', [AdminDashboardController::class, 'financialReport'])->name('financial');
        Route::get('/export', [AdminDashboardController::class, 'exportReport'])->name('export');
    });

    // ========== CONFIGURACIÓN ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'settings'])->name('index');
        Route::post('/update', [AdminDashboardController::class, 'updateSettings'])->name('update');
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories');
        Route::post('/categories', [AdminDashboardController::class, 'updateCategories'])->name('categories.update');
    });
});

// ============================================
// RUTAS ADICIONALES DE SUCURSALES Y PERMISOS (Sistema existente)
// ============================================

// Estas rutas son de tu sistema existente, las mantengo
Route::resource('permissions', PermissionController::class);
Route::resource('user-sucursal', UserSucursalController::class);

Route::prefix('inscripcion')->name('registration.team.')->group(function () {
    Route::get('/equipo', [App\Http\Controllers\PublicRegistrationController::class, 'showForm'])->name('form');
    Route::get('/equipo/plantilla', [App\Http\Controllers\PublicRegistrationController::class, 'downloadTemplate'])->name('download-template');
    Route::post('/equipo', [App\Http\Controllers\PublicRegistrationController::class, 'submitRegistration'])->name('submit');
    Route::get('/exito', [App\Http\Controllers\PublicRegistrationController::class, 'success'])->name('success');
});
