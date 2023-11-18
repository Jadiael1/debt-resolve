<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefaultNotification extends Notification
{
    public string $token;
    public string $subject;
    public array $lines;
    public string $greeting;
    public string $url;


    public function __construct($token = "", $subject = "", $greeting = "", $lines = array(), $url= "")
    {
        $this->token = $token;
        $this->subject = $subject;
        $this->lines = $lines;
        $this->greeting = $greeting;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->greeting)
            ->salutation('Atenciosamente: ' . config('app.name') . ".");
        if (count($this->lines)) {
            foreach ($this->lines as $key => $value) {
                $mailMessage->line($value);
            }
        }
        if(strlen($this->url)){
            $mailMessage->action("Registrar e participar da cobrança", $this->url);

        }
        return $mailMessage;
    }
}
