<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChargeController;
use App\Http\Controllers\API\NewPasswordController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\InstallmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [AuthController::class, 'login']);
Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/signup', [AuthController::class, 'register'])->name('register');


Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::post('/email/resend-activation-link', [EmailVerificationController::class, 'resendActivationLink'])->middleware(['auth:sanctum', 'throttle:2,1'])->name('verification.send');

Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'resetPassword'])->name('password.reset');




Route::get('/charges/{charge_id}/installments', [ChargeController::class, 'listInstallments']);
Route::post('/charges', [ChargeController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/installments/{installment_id}/generate-payment', [InstallmentController::class, 'generatePayment']);
Route::post('/installments/{installment_id}/proof-upload', [InstallmentController::class, 'proofUpload']);



Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});
