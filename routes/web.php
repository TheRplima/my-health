<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\TaskController;
// use App\Http\Controllers\WeatherController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/hello', function () {
    return Inertia::render('Hello');
});

Route::get('/lazy', function () {
    return Inertia::render('LazyPage');
});

Route::middleware('auth')->controller(WaterIntakeController::class)->group(function () {
    Route::get('water-intakes', 'index')->name('water-intakes.index');
    Route::get('water-intake/get-water-intake-by-day', 'getWaterIntakesByDay');
    Route::post('water-intake', 'store')->name('water-intake.store');
    Route::delete('water-intake/{id}', 'destroy');
});

// Route::middleware(['auth', 'admin'])->group(function () {
//     // Admin routes here
//     Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
//     Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
//     Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
//     Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
// });

// Route::controller(WeatherController::class)->group(function () {
//     Route::get('/weather', 'index')->name('weather.index');
//     Route::get('/weather', 'getWeather')->name('get-weather-info');
// });

require __DIR__ . '/auth.php';
