@extends('layouts.store')

@section('title', 'Reset Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Reset Password</h3>
                </div>
                <form action="{{ route('api.store.reset') }}" method="POST" data-callback="resetForm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}" required>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="passwordInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="passwordInput" placeholder="Email Address" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="passwordInput">Password</label>
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPasswordInput">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="confirmPasswordInput" placeholder="Confirm Password" required>
                        </div>
                        @include('layouts.store.hcaptcha')
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
