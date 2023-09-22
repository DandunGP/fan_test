<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');


Route::prefix('user')->group(function () {
    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/', [UserController::class, "get_all_user"]);
        Route::post('/', [UserController::class, "create_user"]);
        Route::get('/{id}', [UserController::class, "get_user"]);
    });
});

Route::prefix('presence')->group(function () {
    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/', [PresenceController::class, "get_all_presence"]);
        Route::post('/', [PresenceController::class, "create_presence"]);
        Route::get('/{id}', [PresenceController::class, "get_presence"]);
        Route::post('/approve', [PresenceController::class, "approve_presence"]);
    });
});