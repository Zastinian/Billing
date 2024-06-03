@php $header_route = "client.ticket.index"; @endphp

@extends('layouts.client')

@section('title', 'Create Support Ticket')
@section('header', 'Support Tickets')

@section('content')
    <form action="{{ route('api.client.ticket.create') }}" method="POST" data-callback="createForm">
        @csrf

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body row">
                        <div class="form-group col-12">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subjectInput" placeholder="Ticket Subject" required>
                        </div>
                        <div class="form-group col-12">
                            <label for="messageInput">Message</label>
                            <textarea type="text" name="message" class="form-control" id="messageInput" placeholder="50 characters required" style="height:200px;" required></textarea>
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('client.ticket.index') }}" class="btn btn-default btn-sm col-lg-1 col-md-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-md-4 offset-1">Create Ticket</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('client_scripts')
    <script>
        function createForm(data) {
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
    </script>
@endsection
