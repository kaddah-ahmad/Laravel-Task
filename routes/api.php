<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
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

Route::group(['prefix' => 'users'], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'users'], function () {
    Route::get('logout', [UserController::class, 'logout']);
    Route::post('admin-register', [UserController::class, 'registerAdmin']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'tasks'], function () {
    Route::post('/', [TaskController::class, 'createTask']);
    Route::get('/{id}', [TaskController::class, 'getTaskById']);
    Route::get('/', [TaskController::class, 'getTasks']);
    Route::put('/{id}', [TaskController::class, 'updateTask']);
    Route::delete('/{id}', [TaskController::class, 'deleteTask']);
});
