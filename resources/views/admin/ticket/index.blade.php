@extends('layouts.admin')

@inject('ticket_model', 'App\Models\Ticket')
@inject('client_model', 'App\Models\Client')

@section('title', 'Support Tickets')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Open Tickets (Waiting for your reply)</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.ticket.create') }}" class="btn btn-success btn-sm">Create Ticket <i class="fas fa-plus"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="open-tickets-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->where('status', 1)->get() as $ticket)
                                <tr>
                                    <td><a href="{{ route('admin.ticket.show', ['id' => $ticket->id]) }}">{{ $ticket->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $ticket->client_id]) }}" target="_blank">{{ $client_model->find($ticket->client_id)->email }}</a></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Tickets (Waiting for clients' reply)</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.ticket.create') }}" class="btn btn-success btn-sm">Create Ticket <i class="fas fa-plus"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="pending-tickets-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->where('status', 2)->get() as $ticket)
                                <tr>
                                    <td><a href="{{ route('admin.ticket.show', ['id' => $ticket->id]) }}">{{ $ticket->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $ticket->client_id]) }}" target="_blank">{{ $client_model->find($ticket->client_id)->email }}</a></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resolved Tickets</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.ticket.create') }}" class="btn btn-success btn-sm">Create Ticket <i class="fas fa-plus"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="resolved-tickets-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->where('status', 0)->get() as $ticket)
                                <tr>
                                    <td><a href="{{ route('admin.ticket.show', ['id' => $ticket->id]) }}">{{ $ticket->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $ticket->client_id]) }}" target="_blank">{{ $client_model->find($ticket->client_id)->email }}</a></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Closed Tickets</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.ticket.create') }}" class="btn btn-success btn-sm">Create Ticket <i class="fas fa-plus"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="closed-tickets-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Locked?</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->where('status', 3)->get() as $ticket)
                                <tr>
                                    <td><a href="{{ route('admin.ticket.show', ['id' => $ticket->id]) }}">{{ $ticket->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $ticket->client_id]) }}" target="_blank">{{ $client_model->find($ticket->client_id)->email }}</a></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
                                        @if ($coupon->is_locked)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>{{ $ticket->updated_at }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Locked?</th>
                                <th>Updated Date</th>
                                <th>Created Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); </script>

    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#open-tickets-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
            $("#pending-tickets-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
            $("#resolved-tickets-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
            $("#closed-tickets-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
