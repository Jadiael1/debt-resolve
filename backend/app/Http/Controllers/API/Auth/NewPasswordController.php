<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;
use stdClass;

class NewPasswordController extends Controller
{
    /**
     * Send a reset password link to the user's email.
     *
     * @OA\Post(
     *      path="/api/v1/auth/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Auth"},
     *      summary="Send reset password link",
     *      description="Send a reset password link to the user's email.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success: Reset password link sent successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Reset password link sent successfully"),
     *              @OA\Property(property="data"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Error: Validation failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}}),
     *          ),
     *      ),
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'success', 'message' => __($status), 'data' => null], 200);
        }

        try {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Reset user's password using the reset password token.
     *
     * @OA\Post(
     *      path="/api/v1/auth/reset-password",
     *      operationId="resetPassword",
     *      tags={"Auth"},
     *      summary="Reset user's password",
     *      description="Reset user's password using the reset password token.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token", "email", "password", "password_confirmation"},
     *              @OA\Property(property="token", type="string", example="reset-token"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success: Password reset successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Password reset successfully"),
     *              @OA\Property(property="data"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error: Internal server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Internal server error"),
     *              @OA\Property(property="errors"),
     *          ),
     *      ),
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                $user->tokens()->delete();
                $this->sendNotificationPasswordHasBeenReseted($request->email, $request->token);
                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['status' => 'success', 'message' => 'Password reset successfully', 'data' => null], 200);
        }
        return response()->json(['status' => 'error', 'message' => __($status), 'errors' => null], 500);
    }

    private function sendNotificationPasswordHasBeenReseted($email, $token)
    {
        $user = User::where('email', $email)->first();
        $token = $token;
        $subject = 'Redefinição de senha realizada com sucesso';
        $lines = array(
            'Gostaríamos de informar que a sua senha foi redefinida com sucesso.',
            'Se você realizou essa ação, você pode ignorar este e-mail. Caso não tenha sido você, por favor, entre em contato conosco imediatamente.',
        );
        $greeting = "Olá " . ucfirst($user->name) . ",";
        $user->notify(new DefaultNotification($token, $subject, $greeting, $lines));
    }
}
