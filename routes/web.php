<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserCodeController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\ProjectSubtypeController;
use App\Http\Controllers\Projects\ProjectTypeController;
use App\Http\Controllers\Trackings\TrackingController;
use App\Http\Controllers\Trackings\ActivityController;

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
Route::get('/', function () {
    return view('welcome');
});

Route::get('/endpoint/images/profile/{filename}', function ($filename) {
    $path = public_path('images/profile/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/endpoint/images/projects/{filename}', function ($filename) {
    $path = public_path('images/projects/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/endpoint/images/activities/{filename}', function ($filename) {
    $path = public_path('images/activities/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/endpoint', function () {
    return view('welcome');
});

Route::post('/endpoint/user/create', [UserController::class, 'create']);

Route::post('/endpoint/user/update', [UserController::class, 'update']);

Route::post('/endpoint/user/login', [UserController::class, 'login']);

Route::post('/endpoint/user/email_exists', [UserController::class, 'emailExists']);

Route::get('/endpoint/user/getSession', [UserController::class, 'getSession']);

Route::get('/endpoint/user/all', [UserController::class, 'all']);

Route::get('/endpoint/user/{id}', [UserController::class, 'show']);


Route::post('/endpoint/user/user_code', [UserController::class, 'searchUserByCode']); 

Route::post('/endpoint/user/generate-code', [UserCodeController::class, 'generateCode']);

Route::post('/endpoint/user/verify-code', [UserCodeController::class, 'verifyCode']);

Route::get('/endpoint/user/show-codes', [UserCodeController::class, 'showCodes']);

Route::get('/endpoint/project/types', [ProjectTypeController::class, 'all']);
Route::post('/endpoint/project/type/store', [ProjectTypeController::class, 'store']);

Route::get('/endpoint/project/subtypes', [ProjectSubtypeController::class, 'all']);
Route::post('/endpoint/project/subtype/store', [ProjectSubtypeController::class, 'store']);

// Projects
Route::get('/endpoint/projects', [ProjectController::class, 'all']);
Route::post('/endpoint/project/store', [ProjectController::class, 'store']);

// Projects entities
Route::post('/endpoint/project/entities/create', [ProjectController::class, 'createProjectEntities']);
Route::get('/endpoint/project/entities/check/{project_id}', [ProjectController::class, 'checkProjectEntities']);

//attachProject
Route::post('/endpoint/project/attach', [ProjectController::class, 'attachProject']); // vincular proyecto a usuario

Route::post('/endpoint/project/detach', [ProjectController::class, 'detachProject']); // desvincular proyecto a usuario

Route::post('/endpoint/project/check-attachment', [ProjectController::class, 'checkAttachmentProjectUser']);

Route::post('/endpoint/user/check-attachment', [UserController::class, 'checkAttachmentUserProject']);


// make user admin 
Route::post('/endpoint/user/makeadmin', [UserController::class, 'makeAdmin']);
// remove user admin
Route::post('/endpoint/user/removeadmin', [UserController::class, 'removeAdmin']);



// Trackings
// obtener todos los trackings
Route::get('/endpoint/trackings', [TrackingController::class, 'trackingAll']);
// obtener trackings por proyecto
Route::get('/endpoint/trackings_project/{project_id}', [TrackingController::class, 'trackingByProject']);
// obtener trackings por semana y proyecto
Route::get('/endpoint/trackings_week/{week_id}/{project_id}', [TrackingController::class, 'trackingByWeekByProject']);
// obtener trackings por semana, proyecto y usuario
Route::get('/endpoint/trackings_week_user/{week_id}/{project_id}/{user_id}', [TrackingController::class, 'trackingByWeekByProjectByUser']);
// obtener semanas de un proyecto
Route::get('/endpoint/weeks/{project_id}/', [TrackingController::class, 'getWeeksByProject']);
// obtener dias de una semana
Route::get('/endpoint/days_week/{week_id}/', [TrackingController::class, 'getDaysByWeek']);
// obtener dias de un proyecto
Route::get('/endpoint/days_project/{project_id}', [TrackingController::class, 'getDaysByProject']);
// crear tracking
Route::post('/endpoint/trackings/create', [TrackingController::class, 'createTracking']);
// crear actividades
Route::post('/endpoint/activities/create', [ActivityController::class, 'createActivity']);
Route::get('/endpoint/activities/all', [ActivityController::class, 'activityAll']);
Route::get('/endpoint/activities/project/{project_id}', [ActivityController::class, 'activityByProject']);
Route::get('/endpoint/activities/tracking/{tracking_id}', [ActivityController::class, 'activityByTracking']);
Route::put('/endpoint/activities/{id}', [ActivityController::class, 'update']);





