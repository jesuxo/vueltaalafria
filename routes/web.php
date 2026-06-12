<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserSucursalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeamPanelController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SpecialController;
use App\Http\Controllers\PhotoDownloadController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\Admin\PhotoUploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* Auth Route::get('signup', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');*/

Route::match(['get','post'],'/',  [SiteController::class, 'index'])->name('site');
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
Route::get('/home', [SiteController::class, 'index'])->name('home');
Route::get('/inicio', [SiteController::class, 'index'])->name('inicio');

// Información pública
Route::get('/informacion', [HomeController::class, 'info'])->name('info');
Route::get('/etapas', [HomeController::class, 'stages'])->name('public.stages');
Route::get('/resultados', [HomeController::class, 'results'])->name('public.results');

// ============================================
// GALERÍA DE FOTOS PÚBLICA (UN SOLO /galeria)
// ============================================
Route::get('/galeria', [PhotoGalleryController::class, 'index'])->name('gallery.index');
Route::get('/galeria/stage/{stage}', [PhotoGalleryController::class, 'getStagePhotos']);
Route::get('/galeria/search', [PhotoGalleryController::class, 'searchPhotos']);
Route::post('/galeria/order', [PhotoGalleryController::class, 'createOrder']);
Route::get('/pedido/{publicCode}', [PhotoGalleryController::class, 'showPublicOrder'])->name('public.order.show');
Route::get('/pedido/{publicCode}/status', [PhotoGalleryController::class, 'checkOrderStatus'])->name('public.order.status');

// ============================================
// INSCRIPCIONES PÚBLICAS
// ============================================
Route::get('/inscripcion/individual', [RegistrationController::class, 'individualForm'])->name('registration.individual.form');
Route::post('/inscripcion/individual', [RegistrationController::class, 'individualSubmit'])->name('registration.individual.submit');
Route::get('/inscripcion/verificar', [RegistrationController::class, 'checkStatus'])->name('registration.check');
Route::post('/inscripcion/verificar', [RegistrationController::class, 'checkStatus'])->name('registration.check.submit');
Route::get('/buscar-estructuras', [TeamController::class, 'searchStructures'])->name('search.structures');
Route::get('/verificar-estructura', [TeamController::class, 'checkStructureExists'])->name('check.structure');
Route::get('/inscripcion/equipo/plantilla', [PublicRegistrationController::class, 'downloadTemplate'])->name('registration.team.download-template');

// ============================================
// INSCRIPCIÓN POR EQUIPOS (PÚBLICA)
// ============================================
Route::prefix('inscripcion')->name('registration.team.')->group(function () {
    Route::get('/equipo', [PublicRegistrationController::class, 'showForm'])->name('form');
    Route::get('/equipo/plantilla', [PublicRegistrationController::class, 'downloadTemplate'])->name('download-template');
    Route::post('/equipo', [PublicRegistrationController::class, 'submitRegistration'])->name('submit');
    Route::get('/exito', [PublicRegistrationController::class, 'success'])->name('success');
});

// ============================================
// PANEL DE EQUIPOS (Acceso con código)
// ============================================
Route::prefix('equipo')->name('team.')->group(function () {
    Route::get('/login', [TeamPanelController::class, 'loginForm'])->name('login');
    Route::post('/login', [TeamPanelController::class, 'login'])->name('login.submit');
    Route::post('/logout', [TeamPanelController::class, 'logout'])->name('logout');

    Route::middleware('team.auth')->group(function () {
        Route::get('/dashboard', [TeamPanelController::class, 'dashboard'])->name('dashboard');
        Route::get('/staff', [TeamPanelController::class, 'staffIndex'])->name('staff');
        Route::post('/staff', [TeamPanelController::class, 'staffStore'])->name('staff.store');
        Route::delete('/staff/{id}', [TeamPanelController::class, 'staffDestroy'])->name('staff.destroy');
        Route::get('/vehiculos', [TeamPanelController::class, 'vehiclesIndex'])->name('vehicles');
        Route::post('/vehiculos', [TeamPanelController::class, 'vehiclesStore'])->name('vehicles.store');
        Route::delete('/vehiculos/{id}', [TeamPanelController::class, 'vehiclesDestroy'])->name('vehicles.destroy');
        Route::get('/fotos', [TeamPanelController::class, 'photosIndex'])->name('photos');
        Route::post('/fotos', [TeamPanelController::class, 'photosStore'])->name('photos.store');
        Route::delete('/fotos/{id}', [TeamPanelController::class, 'photosDestroy'])->name('photos.destroy');
        Route::get('/inscripcion', [TeamPanelController::class, 'registrationForm'])->name('registration');
        Route::post('/inscripcion', [TeamPanelController::class, 'registrationSubmit'])->name('registration.submit');
        Route::get('/descargar-plantilla', [TeamPanelController::class, 'downloadTemplate'])->name('download.template');
        Route::post('/importar-atletas', [TeamPanelController::class, 'importAthletes'])->name('import.athletes');
        Route::get('/atletas', [TeamPanelController::class, 'athletesIndex'])->name('athletes');
    });
});

// ============================================
// PANEL ADMINISTRATIVO DE FOTOS (SIN MIDDLEWARE COMPLEJO - SOLO AUTH)
// ============================================
// IMPORTANTE: Este grupo debe estar ANTES que el grupo admin general
Route::middleware(['auth'])->prefix('admin/fotos')->name('admin.photos.')->group(function () {
    Route::get('/', [PhotoUploadController::class, 'index'])->name('index');
    Route::post('/upload', [PhotoUploadController::class, 'upload']);
    Route::post('/tag/{id}', [PhotoUploadController::class, 'tag']);  // Nota: es 'tag', no 'tagPhoto'
    Route::delete('/{id}', [PhotoUploadController::class, 'destroy']);
});

// ============================================
// PANEL ADMINISTRATIVO GENERAL
// ============================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Descarga de fotos (requiere autenticación o código)
    Route::get('/descargar/foto/{photoId}/{code}', [PhotoDownloadController::class, 'download'])
        ->name('photo.download');

    Route::get('/descargar/todas/{code}', [PhotoDownloadController::class, 'downloadAll'])
        ->name('photo.download.all');

    Route::get('/verificar-pedido/{code}', [PhotoDownloadController::class, 'checkAndGetLinks'])
        ->name('photo.check');

    Route::get('/descargar-foto/{photoId}/{code}', [PhotoDownloadController::class, 'download'])
        ->name('photo.download')
        ->middleware('signed'); // URL firmada por seguridad

    // Dashboard principal
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


    // routes/web.php - Agregar dentro del grupo admin

    Route::prefix('specials')->name('specials.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SpecialController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\SpecialController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\SpecialController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\SpecialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\SpecialController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\SpecialController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Admin\SpecialController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/list', [App\Http\Controllers\Admin\SpecialController::class, 'getList'])->name('list');
    });

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
    Route::get('/stages/{stageId}/schedules', [StageController::class, 'schedules'])->name('stages.schedules');
    Route::post('/stages/{stageId}/schedules', [StageController::class, 'addSchedule'])->name('stages.add-schedule');
    Route::put('/schedules/{scheduleId}', [StageController::class, 'updateSchedule'])->name('stages.update-schedule');
    Route::delete('/schedules/{scheduleId}', [StageController::class, 'deleteSchedule'])->name('stages.delete-schedule');
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
        Route::get('/{id}', [AdminDashboardController::class, 'showRegistration'])->name('show');
        Route::post('/{id}/status', [AdminDashboardController::class, 'updateRegistrationStatus'])->name('status');
    });

    // ========== GESTIÓN DE FOTOS (ADMIN) ==========
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

    Route::get('/registrations/pending/count', [AdminDashboardController::class, 'pendingCount'])->name('registrations.pending.count');
});

// ============================================
// RUTAS ADICIONALES DE SUCURSALES Y PERMISOS (Sistema existente)
// ============================================
Route::resource('permissions', PermissionController::class);
Route::resource('user-sucursal', UserSucursalController::class);
