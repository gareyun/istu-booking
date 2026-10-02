<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BookingForm;
use App\Livewire\Admin\BookingAdminPanel;
use App\Livewire\Admin\Classrooms;
use App\Livewire\TechSupportPanel;
use App\Livewire\ClassroomSchedule;

Route::get('/', BookingForm::class)->name('booking');
Route::get('/booking', BookingForm::class)->name('booking');
Route::get('/admin', BookingAdminPanel::class)->name('admin');
Route::get('/admin/classrooms', Classrooms::class)->name('admin.classrooms');
Route::get('/schedule', ClassroomSchedule::class)->name('schedule');
Route::get('/tech-support', TechSupportPanel::class)->name('tech-support');