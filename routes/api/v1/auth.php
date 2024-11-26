<?php

use App\DefaultPanel\Api\V1\AuthServices;

Route::post('auth/login', [AuthServices::class,'login']);
Route::post('auth/register/pre-step', [AuthServices::class,'preStepRegister']);
Route::post('auth/register', [AuthServices::class,'register']);

Route::post('auth/register/health-data', [AuthServices::class,'updateHealthData'])->middleware('auth:sanctum');

Route::post('auth/verify-account', [AuthServices::class,'verify']);
Route::post('auth/forget-password', [AuthServices::class,'forgetPassword']);
Route::post('auth/send-otp', [AuthServices::class,'sentOtp']);
Route::post('auth/reset-password',[AuthServices::class,'resetPassword']);
Route::post('auth/verify-sms-code', [AuthServices::class,'verifySMSCode']);

