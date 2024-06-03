@extends('layouts.client')

@inject('currency_model', 'App\Models\Currency')
@inject('tax_model', 'App\Models\Tax')

@section('title', 'Account Settings')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Basic Settings</h3>
                </div>
                <form action="{{ route('api.client.account.basic') }}" method="PUT" data-callback="changeSetting">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="currencyInput">Currency</label>
                            <select class="form-control" name="currency">
                                @foreach ($currency_model->all() as $currency)
                                    <option value="{{ $currency->id }}" @if (auth()->user()->currency === $currency->name) selected @endif>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="countryInput">Country</label>
                            <select class="form-control" name="country">
                                @foreach ($tax_model->all() as $tax)
                                    <option value="{{ $tax->id }}" @if (auth()->user()->country === $tax->country) selected @endif>
                                        {{ $tax->country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="autoRenewInput">Enable Automatic Renewal</label>
                            <select class="form-control" name="auto_renew">
                                <option value="1" @if (auth()->user()->auto_renew) selected @endif>Yes</option>
                                <option value="0" @unless (auth()->user()->auto_renew) selected @endunless>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Change Email Address</h3>
                </div>
                <form action="{{ route('api.client.account.email') }}" method="PUT" data-callback="changeEmail">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="emailInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <label for="passwordInput">Current Password</label>
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Current Password" required>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-info">
                                Changing the account email will also change the panel email.
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Change Password</h3>
                </div>
                <form action="{{ route('api.client.account.password') }}" method="PUT" data-callback="changePassword">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="passwordInput">Current Password</label>
                            <input type="password" name="current" class="form-control" id="passwordInput" placeholder="Current Password" required>
                        </div>
                        <div class="form-group">
                            <label for="newPasswordInput">New Password</label>
                            <input type="password" name="password" class="form-control" id="newPasswordInput" placeholder="New Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPasswordInput">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="confirmPasswordInput" placeholder="Confirm Password" required>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-info">
                                Changing the account password will NOT change the panel password.
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

@section('client_scripts')
    <script>
        function changeSetting(data) {
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
    
        function changeEmail(data) {
            if (data.success) {
                toastr.success(data.success)
                resetForms()
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
    
        function changePassword(data) {
            if (data.success) {
                toastr.success(data.success)
                resetForms()
                waitRedirect('{{ route('client.logout') }}')
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
