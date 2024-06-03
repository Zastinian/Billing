@if (config('app.hcaptcha_site_key') && config('app.hcaptcha_secret_key'))
    <div class="form-group">
        <div class="h-captcha" data-sitekey="{{ config('app.hcaptcha_site_key') }}" @if (config('app.dark_mode')) data-theme="dark" @endif></div>
    </div>
@endif
