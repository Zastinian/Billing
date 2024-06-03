<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

class Captcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!config('app.hcaptcha_site_key') || !config('app.hcaptcha_secret_key')) return true;

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query(array('secret' => config('app.hcaptcha_secret_key'), 'response' => $value)));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($verify));

        if ($result->success) session(['bypass_captcha' => 5]);
        if (session('bypass_captcha', 0) > 0) {
            Request::session()->decrement('bypass_captcha', 1);
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please solve the captcha challenge again.';
    }
}
