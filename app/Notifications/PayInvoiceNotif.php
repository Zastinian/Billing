<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Server;
use App\Models\Plan;

class PayInvoiceNotif extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The invoice instance.
     *
     * @var \App\Models\Invoice
     */
    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
        $server_finded = Server::find($this->invoice->server_id);
        if($server_finded == NULL){
            $name = 'Credits';
        } else {
            $plan_finded = Plan::find($server_finded->plan_id);
            $name = $plan_finded->name;
        }
        return (new MailMessage)->subject('Payment of the '.$name.' product')->view('emails.notif', [
            'subject' => 'New Product Payment',
            'body_message' => 'Please pay the '.$name.' product.',
            'body_action' => 'You may click the button below to view the product details, due amount.',
            'button_text' => 'View Product',
            'button_url' => url()->route('client.invoice.show', ['id' => $this->invoice->id]),
            'notice' => 'You received this email because you have ordered a product or service from us.',
        ]);
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
