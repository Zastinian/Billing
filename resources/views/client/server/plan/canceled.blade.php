@extends('layouts.client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i> Your server has been canceled!</h5>
                Your server is being deleted. Thanks for using our services.
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <a href="{{ route('client.server.index') }}" class="btn btn-primary col-lg-3 col-md-6">Go to My Servers</a>
    </div>
@endsection