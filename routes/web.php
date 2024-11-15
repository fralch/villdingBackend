<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserCodeController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\ProjectSubtypeController;
use App\Http\Controllers\Projects\ProjectTypeController;


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
Route::get('/endpoint/images/profile/{filename}', function ($filename) {
    $path = public_path('images/profile/' . $filename);

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

Route::post('/endpoint/user/generate-code', [UserCodeController::class, 'generateCode']);

Route::post('/endpoint/user/verify-code', [UserCodeController::class, 'verifyCode']);

Route::get('/endpoint/user/show-codes', [UserCodeController::class, 'showCodes']);

Route::get('/endpoint/project/types', [ProjectTypeController::class, 'all']);
Route::post('/endpoint/project/type/store', [ProjectTypeController::class, 'store']);

Route::get('/endpoint/project/subtypes', [ProjectSubtypeController::class, 'all']);
Route::post('/endpoint/project/subtype/store', [ProjectSubtypeController::class, 'store']);

Route::get('/endpoint/projects', [ProjectController::class, 'all']);
Route::post('/endpoint/project/store', [ProjectController::class, 'store']);

//attachProject
Route::post('/endpoint/project/attach', [ProjectController::class, 'attachProject']);

Route::post('/endpoint/project/check-attachment', [ProjectController::class, 'checkAttachmentProjectUser']);

Route::post('/endpoint/user/check-attachment', [UserController::class, 'checkAttachmentUserProject']);









