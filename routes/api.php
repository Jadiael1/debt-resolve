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
        Route::get('/{charge_id}/installments', [ChargeController::class, 'listInstallments'])->name('charges.listInstallments');
        Route::post('/invitations', [ChargeController::class, 'chargeInvitation'])->name('charges.chargeInvitation');
        Route::post('/process-charge-invitations/{token}', [ChargeController::class, 'processInvitations'])->name('charges.processInvitations');
        Route::post('/upload-receipt/installments/{installment}', [ChargeController::class, 'uploadReceipt'])->name('charges.uploadReceipt');
        Route::post('/send-payment/installments/{installment}', [ChargeController::class, 'sendPayment'])->name('charges.sendPayment');
        Route::post('/get-payments-for-approval/{charge}', [ChargeController::class, 'getPaymentsForApproval'])->name('charges.getPaymentsForApproval');
        Route::post('{charge}/installments/{installment}/accept-payment-approval-by-collector/', [ChargeController::class, 'acceptPaymentApprovalByCollector'])->name('charges.acceptPaymentApprovalByCollector');
    });

    Route::prefix('installments')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [InstallmentController::class, 'index'])->name('installments.index');
        Route::get('/{installment}', [InstallmentController::class, 'show'])->name('installments.show');

    });

    Route::prefix('charge-invitations')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [ChargeInvitationController::class, 'index'])->name('charge-invitations.index');
        Route::get('/{chargeinvitation}', [ChargeInvitationController::class, 'show'])->name('charge-invitations.show');
        Route::get('/email/{email}', [ChargeInvitationController::class, 'getByEmail'])->name('charge-invitations.getByEmail');
    });

    Route::prefix('users')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/installments/list', [UserController::class, 'installmentList'])->name('users.installmentList');
        Route::get('/charges/list', [UserController::class, 'chargeList'])->name('users.chargeList');
    });
});
