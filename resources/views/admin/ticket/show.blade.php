@php $header_route = "admin.ticket.index"; @endphp

@extends('layouts.admin')

@inject('ticket_content_model', 'App\Models\TicketContent')
@inject('client_model', 'App\Models\Client')

@section('title', 'Ticket #'.$ticket->id)
@section('header', 'Support Tickets')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Ticket Information</h5>
                </div>
                <div class="card-body row">
                    <div class="col-lg-4 col-md-8 mb-1">
                        <h6 class="card-title">Subject</h6>
                        <p class="card-text">{{ $ticket->subject }}</p>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-1">
                        <h6 class="card-title">Status</h6>
                        <p class="card-text">
                            @switch($ticket->status)
                                @case(0)
                                    <span class="badge bg-success">Resolved</span>
                                    @break
                                @case(1)
                                    <span class="badge bg-info">Open</span>
                                    @break
                                @case(2)
                                    <span class="badge bg-warning">Pending</span>
                                    @break
                                @case(3)
                                    <span class="badge bg-danger">Closed</span> 
                                    @if ($ticket->is_locked)
                                        <span class="badge bg-danger">Locked</span>
                                    @endif
                                    @break
                            @endswitch
                        </p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-1">
                        <h6 class="card-title">Created Date</h6>
                        <p class="card-text">{{ $ticket->created_at }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-1">
                        <h6 class="card-title">Updated Date</h6>
                        <p class="card-text">{{ $ticket->updated_at }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-1">
                        <a href="{{ route('admin.ticket.index') }}" class="card-link"><i class="fas fa-arrow-left text-sm"></i> View All Tickets</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="direct-chat-messages">
                        @foreach ($ticket_content_model->where('ticket_id', $id)->oldest()->get() as $ticket_content)
                            @if ($ticket_content->replier_id === $ticket->client_id)
                                <div class="direct-chat-msg right">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-right">{{ $client_model->find($ticket->client_id)->value('email') }}</span>
                                        <span class="direct-chat-timestamp float-left">{{ $ticket_content->created_at }}</span>
                            @else
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-left">{{ $client_model->find($ticket_content->replier_id)->value('email') }}</span>
                                        <span class="direct-chat-timestamp float-right">{{ $ticket_content->created_at }}</span>
                            @endif
                                </div>
                                <div class="direct-chat-text">
                                    {{ $ticket_content->message }}
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card">
                <form action="{{ route('api.admin.ticket.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-header">
                        <h5 class="card-title m-0">Add Reply</h5>
                    </div>
                    <div class="card-body row">
                        <div class="form-group col-12">
                            <label for="messageInput">Message</label>
                            <textarea type="text" name="message" class="form-control" id="messageInput" style="height:200px;"></textarea>
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-3">Reply</button>
                        @if ($ticket->status !== 3)
                            <button type="submit" name="close" value="true" class="btn btn-danger btn-sm col-lg-2 col-3 offset-1">Close</button>
                        @endif
                        @if ($ticket->is_locked)
                            <button type="submit" name="unlock" value="true" class="btn btn-warning btn-sm col-lg-2 col-3 offset-1">Unlock</button>
                        @else
                            <button type="submit" name="lock" value="true" class="btn btn-danger btn-sm col-lg-2 col-3 offset-1">Lock</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function updateForm(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect(window.location.href)
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
