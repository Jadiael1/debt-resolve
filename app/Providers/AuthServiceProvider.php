<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('FRONT_URL_RESET_PASSWORD', 'https://example.com/reset-password?token=') . $token;
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $newDomain = env('FRONT_URL_VERIFY_EMAIL', 'https://example.com');
            $urlScheme = parse_url($url, PHP_URL_SCHEME);
            $urlHost = parse_url($url, PHP_URL_HOST);
            $urlPort = parse_url($url, PHP_URL_PORT) !== null ? ":" . parse_url($url, PHP_URL_PORT) : "";
            // $urlPath = parse_url($url, PHP_URL_PATH);
            $url = str_replace("{$urlScheme}://{$urlHost}{$urlPort}/api/v1/auth/email/verify/", "$newDomain", $url);
            $url = str_replace("/api/v1", "", $url);
            return (new MailMessage)
                ->subject(Lang::get('Verify Email Address'))
                ->line(Lang::get('Please click the button below to verify your email address.'))
                ->action(Lang::get('Verify Email Address'), $url)
                ->line(Lang::get('If you did not create an account, no further action is required.'));
        });
    }
}
