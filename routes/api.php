<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\BookingController;

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

Route::controller(LoginRegisterController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::controller(BookingController::class)->group(function () {
        Route::get('/booking/available', 'index');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BookingController::class)->group(function () {
        Route::get('/booking/details', 'show');
        Route::post('/booking', 'store');
        Route::put('/booking', 'update');
        Route::delete('/booking', 'destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout']);
});
