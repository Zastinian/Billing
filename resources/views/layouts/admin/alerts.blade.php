@if (!config('app.url') || !config('app.panel_url') || !config('app.panel_client_api_key') || !config('app.panel_app_api_key'))
    <div class="alert alert-danger">
        <h5><i class="icon fas fa-exclamation-triangle"></i> Please update your store settings!</h5>
        Click <a href="{{ route('admin.setting.show') }}">here</a> to update them so that HedystiaBilling can function properly, or click 'Reload Config' at the top if you have already done so.
    </div>
@endif
