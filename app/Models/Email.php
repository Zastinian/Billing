<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'client_id',
        'server_id',
        'invoice_id',
        'ticket_id',
        'affiliate_id',
        'subject',
        'body_message',
        'body_action',
        'button_text',
        'button_url',
        'notice',
    ];

    public static function send (
        $subject, $body_message, $body_action, $button_text, $button_url, $notice,
        $client_id, $server_id = null, $invoice_id = null, $ticket_id = null, $affiliate_id = null
    )
    {
        self::create([
            'client_id' => $client_id,
            'server_id' => $server_id,
            'invoice_id' => $invoice_id,
            'ticket_id' => $ticket_id,
            'affiliate_id' => $affiliate_id,
            'subject' => $subject,
            'body_message' => $body_message,
            'body_action' => $body_action,
            'button_text' => $button_text,
            'button_url' => $button_url,
            'notice' => $notice,
        ]);
    }
}
