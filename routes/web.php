<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['message' => 'Welcome to Doctor Appointment API']));

Route::get('/version', fn () => response()->json(['version' => app()->version()]));
