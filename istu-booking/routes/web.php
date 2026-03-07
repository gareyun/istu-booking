<?php

use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClassroomController::class, 'index']);