<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('check-email', [AuthController::class, 'checkEmail']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('generate-otp', [AuthController::class, 'generateOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register-customer', [AuthController::class, 'registerCustomer']);
});
