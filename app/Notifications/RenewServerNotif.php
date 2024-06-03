<?php

namespace App\Notifications;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewServerNotif extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The server instance.
     *
     * @var \App\Models\Server
     */
    protected $server;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
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
        return (new MailMessage)->subject('Your server (#'.$this->server->id.') will expire soon')->view('emails.notif', [
            'subject' => 'Server Expiring',
            'body_message' => 'Please renew your server (#'.$this->server->id.') before the due date or it may be suspended.',
            'body_action' => 'You may click the button below to view and renew your server plan.',
            'button_text' => 'View Server',
            'button_url' => url()->route('client.server.show', ['id' => $this->server->id]),
            'notice' => 'You received this email because you have ordered our products or services.',
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
