@extends('layouts.admin')

@inject('setting_model', 'App\Models\Setting')

@section('title', 'Store Settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.setting.store') }}" method="PUT" data-callback="settingForm">
                    @csrf

                    <div class="card-body row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="companyNameInput">Company Name</label>
                                <input type="text" name="company_name" value="{{ $setting_model->where('key', 'company_name')->value('value') }}" class="form-control" id="companyNameInput" required>
                            </div>
                            <div class="form-group">
                                <label for="storeUrlInput">Store URL (Must include 'https://')</label>
                                <input type="text" name="store_url" value="{{ $setting_model->where('key', 'store_url')->value('value') }}" class="form-control" id="storeUrlInput" placeholder="e.g. https://store.example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="logoPathInput">Logo File Path (Must be inside the 'public' folder)</label>
                                <input type="text" name="logo_path" value="{{ $setting_model->where('key', 'logo_path')->value('value') }}" class="form-control" id="logoPathInput" placeholder="e.g. /img/icon.webp" required>
                            </div>
                            <div class="form-group">
                                <label for="faviconPathInput">Favicon File Path (Must be inside the 'public' folder)</label>
                                <input type="text" name="favicon_path" value="{{ $setting_model->where('key', 'favicon_path')->value('value') }}" class="form-control" id="faviconPathInput" placeholder="e.g. /img/favicon.webp" required>
                            </div>
                            <div class="form-group">
                                <label for="darkModeInput">Enable Dark Mode</label>
                                <select class="form-control" name="dark_mode">
                                    <option value="1" @if ($setting_model->where('key', 'dark_mode')->value('value')) selected @endif>Yes</option>
                                    <option value="0" @unless ($setting_model->where('key', 'dark_mode')->value('value')) selected @endunless>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="openRegistrationInput">Open Registration</label>
                                <select class="form-control" name="open_registration">
                                    <option value="1" @if ($setting_model->where('key', 'open_registration')->value('value')) selected @endif>Yes</option>
                                    <option value="0" @unless ($setting_model->where('key', 'open_registration')->value('value')) selected @endunless>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="panelUrlInput">Panel URL (Must include 'https://')</label>
                                <input type="text" name="panel_url" value="{{ $setting_model->where('key', 'panel_url')->value('value') }}" class="form-control" id="panelUrlInput" placeholder="e.g. https://panel.example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="panelClientApiKeyInput">Panel Client API Key (Must be an administrator)</label>
                                <input type="text" name="panel_client_api_key" value="{{ $setting_model->where('key', 'panel_client_api_key')->value('value') }}" class="form-control" id="panelClientApiKeyInput" required>
                            </div>
                            <div class="form-group">
                                <label for="panelAppApiKeyInput">Panel Application API Key (Must given enough permissions)</label>
                                <input type="text" name="panel_app_api_key" value="{{ $setting_model->where('key', 'panel_app_api_key')->value('value') }}" class="form-control" id="panelAppApiKeyInput" required>
                            </div>
                        </div>
                        <div class="col-lg-5 offset-lg-1">
                            <h5>Optional Settings</h5>
                            <div class="form-group">
                                <label for="phpMyAdminUrlInput">phpMyAdmin URL</label>
                                <input type="text" name="phpmyadmin_url" value="{{ $setting_model->where('key', 'phpmyadmin_url')->value('value') }}" class="form-control" id="phpMyAdminUrlInput" placeholder="e.g. https://pma.example.com">
                            </div>
                            <div class="form-group">
                                <label for="hCaptchaSiteKeyInput">hCaptcha Site Key</label>
                                <input type="text" name="hcaptcha_site_key" value="{{ $setting_model->where('key', 'hcaptcha_site_key')->value('value') }}" class="form-control" id="hCaptchaSiteKeyInput">
                            </div>
                            <div class="form-group">
                                <label for="hCaptchaSecretKeyInput">hCaptcha Secret Key</label>
                                <input type="text" name="hcaptcha_secret_key" value="{{ $setting_model->where('key', 'hcaptcha_secret_key')->value('value') }}" class="form-control" id="hCaptchaSecretKeyInput">
                            </div>
                            <div class="form-group">
                                <label for="analyticsIdInput">Google Analytics</label>
                                <input type="text" name="google_analytics_id" value="{{ $setting_model->where('key', 'google_analytics_id')->value('value') }}" class="form-control" id="analyticsIdInput" placeholder="UA-XXXXXX or G-XXXXXX">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function settingForm(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect('{{ route('admin.cache') }}')
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
    </script>
@endsection
