<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmationEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        // For simplicity, we log the email sending or use the mailable if created
        \Illuminate\Support\Facades\Log::info("Sending order confirmation email for order ID: " . $event->order->id);
        // \Illuminate\Support\Facades\Mail::to($event->order->user->email)->send(new \App\Mail\OrderConfirmationMail($event->order));
    }
}
