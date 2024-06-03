@extends('layouts.client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-warning row justify-content-center">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Are you sure to cancel your server?</h5>
                Your server will be deleted immediately without any refund.
                <button type="submit" form="cancelPlan" class="btn btn-danger col-md-4">Yes</button>
                <a href="{{ route('client.server.plan.show', ['id' => 1]) }}" class="btn btn-primary col-md-4 offset-md-1">No</a>
            </div>
        </div>
    </div>
    <form action="" method="POST" id="cancelPlan"> @csrf </form>
@endsection