<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\WaterIntakeContainersController;
use App\Http\Controllers\WeightControlController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(WaterIntakeController::class)->group(function () {
    Route::get('water-intakes', 'index');
    Route::get('water-intake/get-water-intake-by-day', 'getWaterIntakesByDay');
    Route::post('water-intake', 'store');
    Route::delete('water-intake/{id}', 'destroy');
});

Route::controller(WeightControlController::class)->group(function () {
    Route::get('weight-control', 'index');
    Route::post('weight-control', 'store');
    Route::delete('weight-control/{id}', 'destroy');
});

Route::controller(WaterIntakeContainersController::class)->group(function () {
    Route::get('water-intake-containers', 'index');
    Route::post('water-intake-container', 'store');
    Route::put('water-intake-container/{id}', 'update');
    Route::delete('water-intake-container/{id}', 'destroy');
});
