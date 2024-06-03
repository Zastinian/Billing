@extends('layouts.client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i> You have successfully paid the invoice!</h5>
                Thank you very much for choosing our service.
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <a href="{{ route('client.invoice.show', ['id' => $id]) }}" class="btn btn-primary col-lg-3 col-md-6">View Invoice</a>
    </div>
@endsection