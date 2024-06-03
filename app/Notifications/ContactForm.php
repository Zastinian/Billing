<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactForm extends Notification implements ShouldQueue
{
    use Queueable;

    protected $msg_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->msg_id = $id;
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
        return (new MailMessage)->subject('New message received from contact form')->view('emails.notif', [
            'subject' => 'New Message Received',
            'body_message' => 'You received a new message from the contact form of your HedystiaBilling store.',
            'body_action' => 'Please click the button below to read the message in the admin area.',
            'button_text' => 'View Message',
            'button_url' => url()->route('admin.page.message', ['msg_id' => $this->msg_id]),
            'notice' => 'You may change the email address that receives contact form notifications in the admin area.',
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
