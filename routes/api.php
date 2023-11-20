<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\Auth\NewPasswordController;
use App\Http\Controllers\API\ChargeController;
use App\Http\Controllers\API\InstallmentController;
use App\Http\Controllers\API\ChargeInvitationController;
use App\Http\Controllers\API\UserController;
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

Route::prefix('v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/signup', [AuthController::class, 'signup'])->middleware(['guest'])->name('auth.signup');
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
        Route::post('/signin', [AuthController::class, 'signin'])->middleware(['guest'])->name('auth.signin');
        Route::post('/email/resend-activation-link', [EmailVerificationController::class, 'resendActivationLink'])->middleware(['auth:sanctum', 'throttle:2,1'])->name('verification.send');
        Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword'])->middleware(['guest'])->name('auth.forgotPassword');
        Route::post('/reset-password', [NewPasswordController::class, 'resetPassword'])->middleware(['guest'])->name('auth.resetPassword');
        Route::get('/email/activation-notice', [EmailVerificationController::class, 'notice'])->name('verification.notice');
        Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->middleware(['guest'])->name('login');
    });

    Route::prefix('charges')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [ChargeController::class, 'index'])->name('charges.index');
        Route::get('/{charge}', [ChargeController::class, 'show'])->name('charges.show');
        Route::post('/', [ChargeController::class, 'store'])->name('charges.store');
    });

    Route::prefix('installments')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [InstallmentController::class, 'index'])->name('installments.index');
        Route::get('/{installment}/installment', [InstallmentController::class, 'show'])->name('installments.show');
        Route::get('/charge/{charge}', [InstallmentController::class, 'listInstallments'])->name('installments.listInstallments');
        Route::post('/upload-receipt/{installment}', [InstallmentController::class, 'uploadReceipt'])->name('installments.uploadReceipt');
        Route::post('/send-payment/{installment}', [InstallmentController::class, 'sendPayment'])->name('installments.sendPayment');
        Route::get('/get-payments-for-approval/{charge}/charge', [InstallmentController::class, 'getPaymentsForApproval'])->name('installments.getPaymentsForApproval');
        Route::post('/{installment}/charge/{charge}/accept-payment-approval-by-collector/', [InstallmentController::class, 'acceptPaymentApprovalByCollector'])->name('installments.acceptPaymentApprovalByCollector');
    });

    Route::prefix('charge-invitations')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [ChargeInvitationController::class, 'index'])->name('charge-invitations.index');
        Route::get('/{chargeinvitation}/charge-invitation', [ChargeInvitationController::class, 'show'])->name('charge-invitations.show');
        Route::get('/{email}/email', [ChargeInvitationController::class, 'getByEmail'])->name('charge-invitations.getByEmail');
        Route::post('/invitations', [ChargeInvitationController::class, 'chargeInvitation'])->name('charge-invitations.chargeInvitation');
        Route::post('/process-charge-invitations/{token}', [ChargeInvitationController::class, 'processInvitations'])->name('charge-invitations.processInvitations');
    });

    Route::prefix('users')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}/user', [UserController::class, 'show'])->name('users.show');
        Route::get('/installments', [UserController::class, 'installments'])->name('users.installments');
        Route::get('/charges', [UserController::class, 'charges'])->name('users.charges');
    });
});
