<?php

use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\BookingUserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AdminPanelController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ClassroomSchedule;
use App\Livewire\TechSupportPanel;

Route::get('/', [ClassroomController::class, 'index']);

// Route::get('/admin/classrooms', [AdminPanelController::class, 'classrooms'])->name('admin.classrooms');
// Route::put('/admin/classrooms/{classroom}', [AdminPanelController::class, 'updateClassroom'])->name('classrooms.update');
// Route::delete('/admin/classrooms/{classroom}', [AdminPanelController::class, 'destroyClassroom'])->name('classrooms.destroy');
// Route::post('/admin/classrooms', [AdminPanelController::class, 'store'])->name('classrooms.store');
// Route::post('/admin/buildings', [AdminPanelController::class, 'storeBuilding']);
// Route::post('/admin/building-types', [AdminPanelController::class, 'storeBuildingType']);
// Route::post('/admin/categories', [AdminPanelController::class, 'storeCategory']);

Route::get('/admin/classrooms', function () {
    return view('admin-classrooms');
})->name('admin.classrooms');

Route::get('/booking', function () {
    return view('booking');
});

Route::get('/admin', function() {
    return view('admin');
})->name('admin');

Route::get('/schedule', function() {
    return view('schedule');
})->name('schedule');

Route::get('/tech-support', function() {
    return view('tech-support');
})->name('tech-support');