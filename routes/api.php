<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientAppointmentController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::get('doctors/available', [DoctorController::class, 'available']);
Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

Route::get('appointments/available-slots', [AppointmentController::class, 'availableSlots']);

Route::middleware('auth:api')->group(function () {
    Route::get('doctors/{doctor}/appointments', [DoctorController::class, 'appointments']);
    Route::put('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
    Route::get('patients/{patient}/appointments', [PatientAppointmentController::class, 'index']);
    Route::get('patients/{patient}/appointments/{appointment}', [PatientAppointmentController::class, 'show']);
    Route::post('patients/{patient}/appointments', [PatientAppointmentController::class, 'store']);
    Route::delete('patients/{patient}/appointments/{appointment}', [PatientAppointmentController::class, 'destroy']);
});
