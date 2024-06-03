@extends('layouts.client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i> The plan of your server has been changed!</h5>
                You may need to restart your server to apply the new usage limits.
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <a href="{{ route('client.server.show', ['id' => $id]) }}" class="btn btn-primary col-lg-3 col-md-6">View Server Info</a>
    </div>
@endsection