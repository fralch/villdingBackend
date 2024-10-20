<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

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

