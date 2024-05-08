<?php

use App\Http\Controllers\WaterIntakeContainersController;
use App\Http\Controllers\WeightControlController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use NotificationChannels\Telegram\TelegramUpdates;

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
    Route::post('subscribe-telegram-notifications/{user}', [UserController::class, 'generateTelegramDeeplink'])->name('user.telegram.subscribe');
});

Route::controller(WaterIntakeController::class)->group(function () {
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

Route::controller(WaterIntakeContainersController::class)->group(function () {
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

Route::post('/bot/getupdates', function () {


    // Response is an array of updates.
    $updates = TelegramUpdates::create()
        // (Optional). Get's the latest update. NOTE: All previous updates will be forgotten using this method.
        ->latest()

        // (Optional). Limit to 2 updates (By default, updates starting with the earliest unconfirmed update are returned).
        // ->limit(2)

        // (Optional). Add more params to the request.
        ->options([
            'timeout' => 0,
            'allowed_updates' => "callback_query"
        ])
        ->get();

    if ($updates['ok']) {
        // Chat ID
        $chatId = $updates['result'][0]['callback_query']['message']['chat']['id'];
    }
    // $telegram = new TelegramUpdates();
    // $updates = $telegram->get();
    // foreach ($updates['result'] as $update) {
    //     if (isset($update['callback_query'])) {
    //         $from = $update['callback_query']['from'];
    //         $data = explode('_', $update['callback_query']['data']);
    //         dd($from, $data);
    //     }
    // }
    return (json_encode($updates));
});
