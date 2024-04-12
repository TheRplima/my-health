<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterIngestionController;

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

Route::controller(WaterIngestionController::class)->group(function () {
    Route::get('water-ingestions', 'index');
    Route::get('water-ingestion/get-water-ingestion-by-day', 'getWaterIngestionsByDay');
    Route::post('water-ingestion', 'store');
    Route::delete('water-ingestion/{id}', 'destroy');
});
