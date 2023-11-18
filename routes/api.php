<?php
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\Auth\NewPasswordController;
use App\Http\Controllers\API\ChargeController;
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
        Route::post('/', [ChargeController::class, 'store'])->name('charges.store');
        Route::get('/{charge_id}/installments', [ChargeController::class, 'listInstallments'])->name('charges.listInstallments');
        Route::post('/charge-invitation', [ChargeController::class, 'chargeInvitation'])->name('charges.chargeInvitation');
        Route::post('/process-charge-invitations/{token}', [ChargeController::class, 'processChargeInvitations'])->name('charges.processChargeInvitations');
        Route::get('/charge-invitations/{email}', [ChargeController::class, 'chargeInvitations'])->name('charges.chargeInvitations');
    });
    Route::prefix('installments')->middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/{installment_id}/generate-payment', [InstallmentController::class, 'generatePayment'])->name('installments.generatePayment');
        Route::post('/{installment_id}/proof-upload', [InstallmentController::class, 'proofUpload'])->name('installments.proofUpload');
    });
});
