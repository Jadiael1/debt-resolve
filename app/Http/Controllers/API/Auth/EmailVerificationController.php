<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/auth/email/verify/{user_id}/{hash}",
     *     summary="Verify Email Address",
     *     description="Verify the user's email address using the provided ID and hash",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Email verification hash",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Email verified successfully"),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Error when checking email or verification failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error when checking email"),
     *             @OA\Property(property="errors", type="null", nullable=true)
     *         )
     *     )
     * )
     */
    public function verifyEmail($id, $hash)
    {
        $user = User::find($id);
        if (!$user || !hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['status' => 'error', 'message' => 'Error when checking email', 'errors' => null], 403);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            $this->sendNotificationAccountHasBeenSuccessfullyActivated($user->email, $hash);
            return response()->json(['status' => 'success', 'message' => 'Email verified successfully', 'data' => null], 200);
        }
        return response()->json(['status' => 'success', 'message' => 'Email already verified', 'data' => null], 200);
    }

    public function notice()
    {
        return response()->json(['status' => 'error', 'message' => 'You need to verify your email to access this feature', 'errors' => null], 401);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/auth/email/resend-activation-link",
     *     summary="Resend Activation Link",
     *     description="Resend the activation email link to the user for email verification",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Activation email sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Activation email sent, check your inbox or junk email"),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Registration is already activated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Your registration is already activated"),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     )
     * )
     */
    public function resendActivationLink(Request $request)
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['status' => 'success', 'message' => 'Activation email sent, check your inbox or junk email', 'data' => null], 200);
        }
        return response()->json(['status' => 'success', 'message' => 'Your registration is already activated', 'data' => null], 409);
    }

    private function sendNotificationAccountHasBeenSuccessfullyActivated($email, $token)
    {
        $user = User::where('email', $email)->first();
        $token = $token;
        $subject = 'Conta ativada com sucesso';
        $lines = array(
            'Gostaríamos de informar que a sua conta foi ativada com sucesso.'
        );
        $greeting = "Olá " . ucfirst($user->name) . ",";
        $user->notify(new DefaultNotification($token, $subject, $greeting, $lines));
    }
}
