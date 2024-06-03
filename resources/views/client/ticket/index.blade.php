@extends('layouts.client')

@inject('ticket_model', 'App\Models\Ticket')

@section('title', 'Support Tickets')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Support Tickets</h3>
                    <div class="card-tools">
                        <a href="{{ route('client.ticket.create') }}" class="btn btn-success btn-sm float-right">Create Ticket <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->all() as $ticket)
                                <tr>
                                    <td><a href="{{ route('client.ticket.show', ['id' => $ticket->id]) }}">{{ $ticket->id }}</a></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
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
                                                @break
                                            @default
                                                
                                        @endswitch
                                    </td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
