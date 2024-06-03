@php $header_route = 'admin.client.index'; @endphp

@extends('layouts.admin')

@inject('currency_model', 'App\Models\Currency')
@inject('tax_model', 'App\Models\Tax')

@section('title', $client->email)
@section('header', 'Clients')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <ul class="nav ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.client.show', ['id' => $id]) }}">Settings</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.client.affiliates', ['id' => $id]) }}">Affiliates</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.client.credit', ['id' => $id]) }}">Credit</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Basic Settings</h3>
                </div>
                <form action="{{ route('api.admin.client.basic', ['id' => $id]) }}" method="PUT" data-callback="changeSetting">
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
                <form action="{{ route('api.admin.client.email', ['id' => $id]) }}" method="PUT" data-callback="refreshAfterChange">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="emailInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
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
                <form action="{{ route('api.admin.client.password', ['id' => $id]) }}" method="PUT" data-callback="changePassword">
                    @csrf

                    <div class="card-body">
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
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Account Suspension</h3>
                </div>
                @if ($client->is_active)
                    <form action="{{ route('api.admin.client.suspend', ['id' => $id]) }}" method="POST" data-callback="refreshAfterChange">
                        @csrf

                        <div class="card-body">
                            <div class="alert alert-info">
                                This will also suspend all servers owned by this client.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-danger">Suspend Client</button>
                        </div>
                    </form>
                @else
                    <form action="{{ route('api.admin.client.unsuspend', ['id' => $id]) }}" method="POST" data-callback="refreshAfterChange">
                        @csrf

                        <div class="card-body">
                            <div class="alert alert-info">
                                This will NOT unsuspend any servers owned by this client.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">Unsuspend Client</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Administration</h3>
                </div>
                @if ($client->is_admin)
                    <form action="{{ route('api.admin.client.demote', ['id' => $id]) }}" method="POST" data-callback="refreshAfterChange">
                        @csrf

                        <div class="card-body">This client is an administrator.</div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">Demote Client</button>
                        </div>
                    </form>
                @else
                    <form action="{{ route('api.admin.client.promote', ['id' => $id]) }}" method="POST" data-callback="refreshAfterChange">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <div class="alert alert-warning">
                                    Only promote the user you trust to an administrator, who can access everything inside the admin area!
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-danger">Promote Client to Admin</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Account Deletion</h3>
                </div>
                <form action="{{ route('api.admin.client.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <div class="alert alert-warning">
                                This action cannot be undone!
                            </div>
                            <div class="alert alert-danger">
                                Deleting a client may produce errors if he/she has active server(s). Please consider suspending the account instead.
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger">Delete Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
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
        
        function refreshAfterChange(data) {
            if (data.success) {
                toastr.success(data.success)
                resetForms()
                waitRedirect(window.location.href);
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
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
        
        function deleteForm(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect('{{ route('admin.client.index') }}')
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
