<?php

namespace App\Http\Controllers\Api;

use App\Jobs\CreatePanelUser;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Tax;
use App\Rules\Captcha;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:clients',
            'password' => 'required|string',
            'h-captcha-response' => new Captcha,
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $throttle_key = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttle_key, 5)) {
            $seconds = RateLimiter::availableIn($throttle_key);
            return $this->respondJson(['error' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])]);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember')))
            return $this->respondJson(['error' => __('auth.failed')]);
        
        $request->session()->regenerate();

        if (!$request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return $this->respondJson(['error' => 'Your account has been suspended!']);
        }

        session([
            'currency' => Currency::where('name', auth()->user()->currency)->first(),
            'tax' => Tax::where('country', auth()->user()->country)->first(),
        ]);
        
        $url = session('after_login_url', null);
        $request->session()->forget('after_login_url');

        return $this->respondJson([
            'success' => 'Welcome back! Redirecting...',
            'url' => $url,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|confirmed|min:8|max:255',
            'terms' => 'required|accepted',
            'h-captcha-response' => new Captcha,
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        Auth::login($client = Client::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referer_id' => session('referer_id'),
            'currency' => session('currency')->name,
            'country' => session('tax')->country,
            'timezone' => 'UTC',
            'language' => 'EN',
        ]));

        if (session('referer_id')) {
            $referer = Client::find(session('referer_id'));
            $referer->sign_ups += 1;
            $referer->save();
        }

        event(new Registered($client));

        CreatePanelUser::dispatch($client)->onQueue('high');

        return $this->respondJson(['success' => 'Account registered successfully! Redirecting...']);
    }

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:clients',
            'h-captcha-response' => new Captcha,
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT ?
            $this->respondJson(['success' => 'We have sent you an email. Please click the link inside to reset your password.']) :
            $this->respondJson(['error' => __($status)]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email|exists:clients',
            'password' => 'required|string|confirmed|min:8',
            'h-captcha-response' => new Captcha,
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET ?
            $this->respondJson(['success' => 'Your password has been reset! You may now log into your account.']) :
            $this->respondJson(['error' => __($status)]);
    }
}
