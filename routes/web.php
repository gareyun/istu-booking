<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BookingForm;
use App\Livewire\Admin\BookingAdminPanel;
use App\Livewire\Admin\Classrooms;
use App\Livewire\TechSupportPanel;
use App\Livewire\ClassroomSchedule;
use App\Http\Controllers\AuthController;


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', BookingAdminPanel::class)->name('index');
        Route::get('/classrooms', Classrooms::class)->name('classrooms');
    });

Route::middleware(['auth', 'tech'])->group(function () {
    Route::get('/tech-support', TechSupportPanel::class)->name('tech-support');
});

Route::middleware('auth')->group(function () {

    Route::get('/', BookingForm::class)->name('booking');
    Route::get('/schedule', ClassroomSchedule::class)->name('schedule');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);