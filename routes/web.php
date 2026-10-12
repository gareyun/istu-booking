<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BookingForm;
use App\Livewire\Admin\BookingAdminPanel;
use App\Livewire\Admin\Classrooms;
use App\Livewire\TechSupportPanel;
use App\Livewire\ClassroomSchedule;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\WebActivationController;

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

Route::get('/email/verify/{user}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.verify');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/activate', [WebActivationController::class, 'showRequest'])->name('activate');
    Route::post('/activate', [WebActivationController::class, 'requestCode'])
        ->middleware('throttle:5,1')
        ->name('activate.request');
    Route::get('/activate/code', [WebActivationController::class, 'showCode'])->name('activate.code');
    Route::post('/activate/code', [WebActivationController::class, 'confirm'])
        ->middleware('throttle:10,1')
        ->name('activate.confirm');
});
