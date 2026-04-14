<?php

use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\BookingUserController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClassroomController::class, 'index']);
Route::get('/admin', [BookingAdminController::class, 'index'])->name('admin');
Route::get('/booking', [BookingUserController::class, 'index']);

Route::post('/bookings', [BookingUserController::class, 'store']);
Route::post('/bookings/{booking}/status', [BookingAdminController::class, 'updateStatus'])->name('booking.updateStatus');
Route::post('/admin/classrooms', [BookingAdminController::class, 'store'])->name('classrooms.store');

Route::get('/admin/classrooms', [BookingAdminController::class, 'classrooms'])->name('admin.classrooms');
Route::put('/admin/classrooms/{classroom}', [BookingAdminController::class, 'updateClassroom'])->name('classrooms.update');

Route::delete('/admin/classrooms/{classroom}', [BookingAdminController::class, 'destroyClassroom'])->name('classrooms.destroy');