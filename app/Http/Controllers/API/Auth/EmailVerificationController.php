<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{

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

    public function resendActivationLink(Request $request)
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['status' => 'success', 'message' => 'Activation email sent, check your inbox or junk email', 'data' => null], 200);
        }
        return response()->json(['status' => 'success', 'message' => 'Your registration is already activated', 'data' => null], 200);
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
