<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserWebController;
use App\Http\Controllers\User\UserCodeController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\ProjectSubtypeController;
use App\Http\Controllers\Projects\ProjectTypeController;
use App\Http\Controllers\Trackings\TrackingController;
use App\Http\Controllers\Trackings\ActivityController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================================================
// RUTAS PRINCIPALES
// ============================================================================
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/endpoint', function () {
    return view('welcome');
});

// ============================================================================
// LOGIN BÁSICO PARA ADMIN (Protege las rutas /admin/users)
// ============================================================================
// Rutas de login simples con credenciales prefijadas (env o hardcode)
Route::prefix('admin')->group(function () {
    // Mostrar formulario de login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('simple_login.show');

    // Procesar login
    Route::post('/login', function (Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $expectedUser = env('SIMPLE_LOGIN_USER', 'admin');
        $expectedPass = env('SIMPLE_LOGIN_PASSWORD', 'password');

        if ($request->input('username') === $expectedUser && $request->input('password') === $expectedPass) {
            $request->session()->put('simple_login', true);
            return redirect()->route('users.index');
        }

        return back()->withInput()->with('error', 'Credenciales inválidas');
    })->name('simple_login.do');

    // Cerrar sesión
    Route::post('/logout', function (Request $request) {
        $request->session()->forget('simple_login');
        $request->session()->regenerateToken();
        return redirect()->route('simple_login.show');
    })->name('simple_login.logout');
});

// ============================================================================
// RUTAS WEB DE GESTIÓN DE USUARIOS (Vistas Blade)
// ============================================================================
// Estas rutas utilizan el prefijo /admin/users y se protegen con el middleware simple.login
Route::prefix('admin')->middleware('simple.login')->name('users.')->group(function () {
    // Redirección base de /admin según estado de sesión
    Route::get('/', function () {
        return redirect()->route('users.index');
    });
    Route::get('/users', [UserWebController::class, 'index'])->name('index');
    Route::get('/users/create', [UserWebController::class, 'create'])->name('create');
    Route::post('/users', [UserWebController::class, 'store'])->name('store');
    Route::get('/users/{id}', [UserWebController::class, 'show'])->name('show');
    Route::get('/users/{id}/edit', [UserWebController::class, 'edit'])->name('edit');
    Route::put('/users/{id}', [UserWebController::class, 'update'])->name('update');
    Route::delete('/users/{id}', [UserWebController::class, 'destroy'])->name('destroy');
});

// ============================================================================
// RUTAS DE USUARIOS (API Endpoints)
// ============================================================================

// Gestión básica de usuarios
Route::post('/endpoint/user/create', [UserController::class, 'create']);
Route::post('/endpoint/user/update', [UserController::class, 'update']);
Route::post('/endpoint/user/login', [UserController::class, 'login']);
Route::post('/endpoint/user/email_exists', [UserController::class, 'emailExists']);
Route::get('/endpoint/user/getSession', [UserController::class, 'getSession']);
Route::get('/endpoint/user/all', [UserController::class, 'all']);
Route::get('/endpoint/user/{id}', [UserController::class, 'show']);
Route::post('/endpoint/user/user_code', [UserController::class, 'searchUserByCode']);

// Códigos de usuario
Route::post('/endpoint/user/generate-code', [UserCodeController::class, 'generateCode']);
Route::post('/endpoint/user/verify-code', [UserCodeController::class, 'verifyCode']);
Route::get('/endpoint/user/show-codes', [UserCodeController::class, 'showCodes']);

// Administración de usuarios
Route::post('/endpoint/user/makeadmin', [UserController::class, 'makeAdmin']);
Route::post('/endpoint/user/removeadmin', [UserController::class, 'removeAdmin']);

// Vinculación usuario-proyecto
Route::post('/endpoint/user/check-attachment', [UserController::class, 'checkAttachmentUserProject']);

// ============================================================================
// RUTAS DE PROYECTOS
// ============================================================================

// Tipos de proyecto
Route::get('/endpoint/project/types', [ProjectTypeController::class, 'all']);
Route::post('/endpoint/project/type/store', [ProjectTypeController::class, 'store']);
Route::get('/endpoint/project/types/{project_id}', [ProjectController::class, 'getProjectTypes']);

// Subtipos de proyecto
Route::get('/endpoint/project/subtypes', [ProjectSubtypeController::class, 'all']);
Route::post('/endpoint/project/subtype/store', [ProjectSubtypeController::class, 'store']);
Route::get('/endpoint/project/subtypes/{project_id}', [ProjectController::class, 'getProjectSubtypes']);

// Gestión de proyectos
Route::get('/endpoint/projects', [ProjectController::class, 'all']);
Route::post('/endpoint/project/store', [ProjectController::class, 'store']);
Route::post('/endpoint/project/update/{id}', [ProjectController::class, 'updateProject']);
Route::delete('/endpoint/project/destroy/{id}', [ProjectController::class, 'destroyProject']);

// Entidades de proyecto
Route::post('/endpoint/project/entities/create', [ProjectController::class, 'createProjectEntities']);
Route::get('/endpoint/project/entities/check/{project_id}', [ProjectController::class, 'checkProjectEntities']);

// Vinculación proyecto-usuario
Route::post('/endpoint/project/attach', [ProjectController::class, 'attachProject']);
Route::post('/endpoint/project/detach', [ProjectController::class, 'detachProject']);
Route::post('/endpoint/project/check-attachment', [ProjectController::class, 'checkAttachmentProjectUser']);

// ============================================================================
// RUTAS DE SEGUIMIENTOS (Trackings)
// ============================================================================

// Obtener los seguimientos (Trackings)
Route::get('/endpoint/trackings', [TrackingController::class, 'trackingAll']);
Route::get('/endpoint/trackings/with-trashed', [TrackingController::class, 'trackingAllWithTrashed']);
Route::get('/endpoint/trackings/only-trashed', [TrackingController::class, 'trackingOnlyTrashed']);
Route::get('/endpoint/trackings_project/{project_id}', [TrackingController::class, 'trackingByProject']);
Route::get('/endpoint/trackings_project_with_finish/{project_id}', [TrackingController::class, 'trackingByProjectWithTrashed']);
Route::get('/endpoint/trackings_week/{week_id}/{project_id}', [TrackingController::class, 'trackingByWeekByProject']);
Route::get('/endpoint/trackings_week_user/{week_id}/{project_id}/{user_id}', [TrackingController::class, 'trackingByWeekByProjectByUser']);

// Gestión de trackings
Route::post('/endpoint/trackings/create', [TrackingController::class, 'createTracking']);
Route::post('/endpoint/tracking/update-title/{id}', [TrackingController::class, 'updateTrackingTitle']);
Route::post('/endpoint/tracking/delete/{id}', [TrackingController::class, 'deleteTracking']);
Route::post('/endpoint/tracking/restore/{id}', [TrackingController::class, 'restoreTracking']);
Route::delete('/endpoint/tracking/force-delete/{id}', [TrackingController::class, 'forceDeleteTracking']);

// Gestión de trackings por tiempo
Route::get('/endpoint/weeks/{project_id}/', [TrackingController::class, 'getWeeksByProject']);
Route::get('/endpoint/days_week/{week_id}/', [TrackingController::class, 'getDaysByWeek']);
Route::get('/endpoint/days_project/{project_id}', [TrackingController::class, 'getDaysByProject']);

// Reportes de tracking
Route::post('/endpoint/tracking/report/daily/{tracking_id}', [TrackingController::class, 'generateDailyReport']);
Route::post('/endpoint/tracking/report/multi', [TrackingController::class, 'generateMultiReport']);

// ============================================================================
// RUTAS DE ACTIVIDADES
// ============================================================================

// Obtener actividades
Route::get('/endpoint/activities/all', [ActivityController::class, 'activityAll']);
Route::get('/endpoint/activities/project/{project_id}', [ActivityController::class, 'activityByProject']);
Route::get('/endpoint/activities/tracking/{tracking_id}', [ActivityController::class, 'activityByTracking']);

// Gestión de actividades
Route::post('/endpoint/activities/create', [ActivityController::class, 'createActivity']);
Route::post('/endpoint/activities/{id}', [ActivityController::class, 'updateActivity']);
Route::post('/endpoint/activities_imgs/{id}', [ActivityController::class, 'updateActivityWithImages']);
Route::post('/endpoint/activities_complete', [ActivityController::class, 'completeActivity']);
Route::post('/endpoint/activities_check/{id}', [ActivityController::class, 'updateActivityStatusByDate']);
Route::post('/endpoint/activities_delete/{id}', [ActivityController::class, 'deleteActivity']);