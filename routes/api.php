<?php

use App\Http\Controllers\WaterIntakeContainerController;
use App\Http\Controllers\WeightControlController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhysicalActivityCategoryController;
use App\Http\Controllers\PhysicalActivityController;
use App\Http\Controllers\PhysicalActivitySportController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(UserController::class)->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('user.index');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{id?}', [UserController::class, 'show'])->name('user.show');
    Route::match(['put', 'patch'], 'user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('generate-telegram-deeplink/{user}', [UserController::class, 'generateTelegramDeeplink'])->name('user.telegram.generate-deeplink');
    Route::get('notification-channels-list', [UserController::class, 'getNotificationChannelsList'])->name('user.notification-channels-list');
    Route::post('subscribe-notification-channel/{user}/{channel}', [UserController::class, 'subscribeNotificationChannel'])->name('user.subscribe-notification-channel');
    Route::post('unsubscribe-notification-channel/{user}/{channel}', [UserController::class, 'unsubscribeNotificationChannel'])->name('user.unsubscribe-notification-channel');
});

Route::middleware('auth:api')->controller(WaterIntakeController::class)->group(function () {
    Route::get('water-intakes', 'index');
    Route::get('water-intake/get-water-intake-by-day', 'getWaterIntakesByDay');
    Route::post('water-intake', 'store')->name('water-intake.store');
    Route::delete('water-intake/{id}', 'destroy');
});

Route::controller(WeightControlController::class)->group(function () {
    Route::get('weight-control', 'index');
    Route::post('weight-control', 'store');
    Route::delete('weight-control/{id}', 'destroy');
});

Route::controller(WaterIntakeContainerController::class)->group(function () {
    Route::get('water-intake-containers', 'index');
    Route::post('water-intake-container', 'store');
    Route::put('water-intake-container/{id}', 'update');
    Route::delete('water-intake-container/{id}', 'destroy');
});

Route::controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index');
    Route::get('/notifications-unread', 'indexUnreadNotifications');
    Route::patch('/mark-notification/{id}', 'markNotification');
    Route::patch('/mark-notifications', 'markAllNotifications');
    Route::delete('/notification/{id}', 'destroy');
});

//route group with  'prefix' => 'physical-activities' to create a group of routes involving physical activities, categories and sports
Route::group(['prefix' => 'physical-activities'], function () {
    Route::controller(PhysicalActivityCategoryController::class)->group(function () {
        Route::get('categories', 'index');
        Route::post('category', 'store');
        Route::put('category/{id}', 'update');
        Route::delete('category/{id}', 'destroy');
    });
    Route::controller(PhysicalActivitySportController::class)->group(function () {
        Route::get('sports', 'index');
        Route::post('sport', 'store');
        Route::put('sport/{id}', 'update');
        Route::delete('sport/{id}', 'destroy');
    });
    Route::controller(PhysicalActivityController::class)->group(function () {
        Route::get('activities', 'index');
        Route::post('activity', 'store');
        Route::put('activity/{id}', 'update');
        Route::delete('activity/{id}', 'destroy');
    });
});
