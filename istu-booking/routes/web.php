<?php

use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\BookingUserController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClassroomController::class, 'index']);
Route::get('/admin', [BookingAdminController::class, 'index']);
Route::get('/booking', [BookingUserController::class, 'index']);

Route::post('/bookings', [BookingUserController::class, 'store']);