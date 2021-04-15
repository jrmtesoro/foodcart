<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $order_information;


    public function __construct($order_information)
    {
        $this->order_information = $order_information;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.order_confirmation', [
            "restaurant_list" => $this->order_information['restaurant_list'],
            "order" => $this->order_information['order'],
            "grand_total" => $this->order_information['grand_total'],
            "customer" => $this->order_information['customer']
        ]);
    }
}
