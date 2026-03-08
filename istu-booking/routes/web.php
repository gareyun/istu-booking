<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClassroomController::class, 'index']);
Route::get('/admin', [BookingController::class, 'index']);