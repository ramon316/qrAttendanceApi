<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\EventController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

/* public routes */
Route::post('/register', [AuthController::class,'register'])->name('register');
Route::post('/login', [AuthController::class,'login'])->name('login');

/* public route event info by qr code */
Route::get('/events/qr/{qrCode}', [EventController::class,'getByQR'])->name('events.qr');

/* private routes */
Route::middleware('auth:sanctum')->group(function () {
    /* auth */
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::get('/profile', [AuthController::class,'profile'])->name('profile');
    Route::get('/check-status', [AuthController::class,'checkStatus'])->name('check-status');

    /* events */
    Route::get('/events', [EventController::class,'index'])->name('events.index');
    Route::post('/events', [EventController::class,'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class,'show'])->name('events.show');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.delete');

    /* attendances */
    Route::post('/attendances', [AttendanceController::class,'checkIn'])->name('attendances.checkIn');
    Route::get('/attendances/my', [AttendanceController::class,'myAttendances'])->name('attendances.my');
    Route::get('/attendances/event/{event}', [AttendanceController::class,'eventAttendances'])->name('attendances.event');

});

/* test routes */
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Test route',
    ], 200);
})->name('test');
