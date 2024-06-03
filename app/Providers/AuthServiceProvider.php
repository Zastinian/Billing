<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)->subject('Please verify your ' . config('app.company_name') . ' account')->view('emails.notif', [
                'subject' => 'Verify Your Account',
                'body_message' => 'Thank you for registering for an account on our website. It\'s nice to meet you.',
                'body_action' => 'Before using our services, please verify your email address by clicking the button below.',
                'button_text' => 'Verify Account',
                'button_url' => $url,
                'notice' => 'You may safely ignore this email if you didn\'t sign up for an account.',
            ]);
        });
    }
}
