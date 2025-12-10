<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PatientAppointmentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
});

Route::get('doctors/available', [DoctorController::class, 'available']);
Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::get('appointments', [PatientAppointmentController::class, 'index']);
    Route::post('appointments', [PatientAppointmentController::class, 'store']);
    Route::get('appointments/{appointment}', [PatientAppointmentController::class, 'show']);
    Route::put('appointments/{appointment}/status', [PatientAppointmentController::class, 'updateStatus']);

    Route::post('chats', [ChatController::class, 'startConversation']);
    Route::get('chats', [ChatController::class, 'index']);
    Route::get('chats/{chat}', [ChatController::class, 'show']);
    Route::patch('chats/{chat}/status', [ChatController::class, 'updateStatus']);
    Route::get('chats/{chat}/messages', [ChatController::class, 'getMessages']);
    Route::post('chats/{chat}/messages', [ChatController::class, 'sendMessage']);
    Route::patch('messages/{message}/read', [ChatController::class, 'markAsRead']);

    Route::post('payments/intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('payments/confirm', [PaymentController::class, 'confirmPayment']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
