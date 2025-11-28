<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientAppointmentController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
});

Route::get('doctors/available', [DoctorController::class, 'available']);
Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::get('doctors/{doctor}/appointments', [DoctorController::class, 'appointments']);
    Route::get('appointments', [PatientAppointmentController::class, 'index']);
    Route::post('appointments', [PatientAppointmentController::class, 'store']);
    Route::get('appointments/{appointment}', [PatientAppointmentController::class, 'show']);
    Route::put('appointments/{appointment}/status', [PatientAppointmentController::class, 'updateStatus']);
    Route::delete('appointments/{appointment}', [PatientAppointmentController::class, 'destroy']);
});
