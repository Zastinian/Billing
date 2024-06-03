@extends('layouts.store')

@section('content')
    <div class="row justify-content-center">
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Your account has not yet been verified!</h5>
            Please click the link inside the verification email to verify your account. If you did not receive it, please click <a href="{{ route('verification.send') }}">here</a> to send the email again.
        </div>
    </div>
@endsection