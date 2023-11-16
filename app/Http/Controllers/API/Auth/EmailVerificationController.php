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
            return response()->json(['message' => 'Error when checking email'], 403);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            $this->sendNotificationAccountHasBeenSuccessfullyActivated($user->email, $hash);
            return response()->json(['message' => 'Email verified successfully'], 200);
        }
        return response()->json(['message' => 'Email already verified'], 200);
    }

    public function notice()
    {
        return response()->json(['message' => 'You need to verify your email to access this feature'], 401);
    }

    public function resendActivationLink(Request $request)
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Activation email sent, check your inbox or junk email'], 200);
        }
        return response()->json(['message' => 'Your registration is already activated'], 200);
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
