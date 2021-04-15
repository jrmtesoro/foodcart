<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->line('Thanks for creating an account with Pinoy Food Cart. To continue, please confirm your email address by clicking the button below. ')
                ->line('Email Verification Token')
                ->line('Code: '.$this->token)
                ->line('By registering your email address to Pinoy Food Cart, you will be able to use the service on your PC in addition to your smartphone.')
                ->action('Verify Email Address', route('guest.verify', ['verification_token' => $this->token]))
                ->line("You'\re receiving this email because you (or someone using this email) created an account on Pinoy Food Cart using this address.
                If you didn't recently attempt to create an account with this email address, you can safely disregard this email.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
