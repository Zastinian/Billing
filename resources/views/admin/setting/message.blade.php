@extends('layouts.client')

@section('title', 'View Message')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Message Information</h5>
                </div>
                <div class="card-body row">
                    <div class="col-lg-4 col-md-6 mb-1">
                        <h6 class="card-title">Email</h6>
                        <p class="card-text">{{ $message->email }}</p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-1">
                        <h6 class="card-title">Name</h6>
                        <p class="card-text">{{ $message->name }}</p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <h6 class="card-title">Received Date</h6>
                        <p class="card-text">{{ $message->created_at }}</p>
                    </div>
                    <div class="col-lg-8 col-md-6 mb-1">
                        <h6 class="card-title">Subject</h6>
                        <p class="card-text">{{ $message->subject }}</p>
                    </div>
                    <div class="col-lg-4 mb-1">
                        <a href="{{ route('admin.page.contact') }}" class="card-link"><i class="fas fa-arrow-left text-sm"></i> View All Messages</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">{{ $message->message }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
