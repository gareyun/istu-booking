<?php

use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\BookingUserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AdminPanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClassroomController::class, 'index']);
Route::get('/admin', [BookingAdminController::class, 'index'])->name('admin');
Route::get('/booking', [BookingUserController::class, 'index']);
// Route::get('/bookings/busy-slots', [BookingUserController::class, 'getBusySlots']);

// Route::post('/bookings', [BookingUserController::class, 'store']);
Route::post('/bookings/{booking}/status', [BookingAdminController::class, 'updateStatus'])->name('booking.updateStatus');
Route::post('/admin/classrooms', [AdminPanelController::class, 'store'])->name('classrooms.store');

Route::get('/admin/classrooms', [AdminPanelController::class, 'classrooms'])->name('admin.classrooms');
Route::put('/admin/classrooms/{classroom}', [AdminPanelController::class, 'updateClassroom'])->name('classrooms.update');
Route::delete('/admin/classrooms/{classroom}', [AdminPanelController::class, 'destroyClassroom'])->name('classrooms.destroy');

Route::post('/admin/buildings', [AdminPanelController::class, 'storeBuilding']);
Route::post('/admin/building-types', [AdminPanelController::class, 'storeBuildingType']);
Route::post('/admin/categories', [AdminPanelController::class, 'storeCategory']);

Route::get('/booking', function () {
    return view('booking');
});