<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AcceptOrder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $order_code;
    protected $restaurant_name;
    protected $eta;

    public function __construct($order_code, $restaurant_name, $eta)
    {
        $this->order_code = $order_code;
        $this->restaurant_name = $restaurant_name;
        $this->eta = $eta;
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
                    ->subject('Order Accepted')
                    ->line('This message was sent to inform you that your order #'.$this->order_code." has been accepted.")
                    ->line($this->restaurant_name." will deliver your order in ".$this->eta." mins.")
                    ->line('Thank you for ordering!');
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
