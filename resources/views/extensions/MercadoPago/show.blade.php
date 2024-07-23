@extends('layouts.admin')

@inject('extension_model', 'App\Models\Extension')

@section('title', 'MercadoPago Settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.extension.update', ['id' => 'MercadoPago']) }}" method="PUT" data-callback="settingForm">
                    @csrf
                    <div class="card-body row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="modeInput">Enabled</label>
                                <select class="form-control" name="enabled">
                                    <option value="1" @if ($extension_model->where(['extension' => 'MercadoPago', 'key' => 'enabled'])->value('value') === '1') selected @endif>Enabled</option>
                                    <option value="0" @if ($extension_model->where(['extension' => 'MercadoPago', 'key' => 'enabled'])->value('value') === '0') selected @endif>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="secretInput">Access Token</label>
                                <input type="text" name="access_token" value="{{ $extension_model->where(['extension' => 'MercadoPago', 'key' => 'access_token'])->value('value') }}" class="form-control" id="secretInput" required>
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
