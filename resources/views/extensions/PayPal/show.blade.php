@extends('layouts.admin')

@inject('extension_model', 'App\Models\Extension')

@section('title', 'PayPal Settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.extension.update', ['id' => 'PayPal']) }}" method="PUT" data-callback="settingForm">
                    @csrf

                    <div class="card-body row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="modeInput">Enabled</label>
                                <select class="form-control" name="enabled">
                                    <option value="1" @if ($extension_model->where(['extension' => 'PayPal', 'key' => 'enabled'])->value('value') === '1') selected @endif>Enabled</option>
                                    <option value="0" @if ($extension_model->where(['extension' => 'PayPal', 'key' => 'enabled'])->value('value') === '0') selected @endif>Disabled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="modeInput">Mode</label>
                                <select class="form-control" name="mode">
                                    <option value="live" @if ($extension_model->where(['extension' => 'PayPal', 'key' => 'mode'])->value('value') === 'live') selected @endif>Live</option>
                                    <option value="sandbox" @if ($extension_model->where(['extension' => 'PayPal', 'key' => 'mode'])->value('value') === 'sandbox') selected @endif>Sandbox</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="usernameInput">Username</label>
                                <input type="text" name="username" value="{{ $extension_model->where(['extension' => 'PayPal', 'key' => 'username'])->value('value') }}" class="form-control" id="usernameInput" required>
                            </div>
                            <div class="form-group">
                                <label for="passwordInput">Password</label>
                                <input type="text" name="password" value="{{ $extension_model->where(['extension' => 'PayPal', 'key' => 'password'])->value('value') }}" class="form-control" id="passwordInput" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="secretInput">Secret/Signature</label>
                                <input type="text" name="secret" value="{{ $extension_model->where(['extension' => 'PayPal', 'key' => 'secret'])->value('value') }}" class="form-control" id="secretInput" required>
                            </div>
                            <div class="form-group">
                                <label for="certificateInput">Certificate (Optional)</label>
                                <input type="text" name="certificate" value="{{ $extension_model->where(['extension' => 'PayPal', 'key' => 'certificate'])->value('value') }}" class="form-control" id="certificateInput">
                            </div>
                            <div class="form-group">
                                <label for="appIdInput">App ID (Optional)</label>
                                <input type="text" name="app_id" value="{{ $extension_model->where(['extension' => 'PayPal', 'key' => 'app_id'])->value('value') }}" class="form-control" id="appIdInput">
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
