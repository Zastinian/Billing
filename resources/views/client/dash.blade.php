@extends('layouts.client')

@inject('server_model', 'App\Models\Server')
@inject('plan_model', 'App\Models\Plan')
@inject('invoice_model', 'App\Models\Invoice')
@inject('tax_model', 'App\Models\Tax')
@inject('ticket_model', 'App\Models\Ticket')
@inject('credit_model', 'App\Models\Credit')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{!! session('currency')->symbol !!}{{ auth()->user()->credit * session('currency')->rate }} {{ session('currency')->name }}</h3>
                    <p>Account Credit</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('client.credit.show') }}" class="small-box-footer">
                    Add Fund <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $server_model->where(['client_id' => auth()->user()->id, 'status' => 0])->count() }}</h3>
                    <p>Active Servers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <a href="{{ route('client.server.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $invoice_model->where(['client_id' => auth()->user()->id, 'paid' => false])->count() }}</h3>
                    <p>Unpaid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('client.invoice.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $ticket_model->where(['client_id' => auth()->user()->id, 'status' => 2])->count() }}</h3>
                    <p>Pending Tickets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="{{ route('client.ticket.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Servers</h3>
                    <div class="card-tools">
                        <a href="{{ route('client.server.index') }}" class="btn btn-default btn-sm">View All Servers <i class="fas fa-arrow-circle-right"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Server Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_model->where(['client_id' => auth()->user()->id, 'status' => 0])->get() as $server)
                                <tr>
                                    <td><a href="{{ route('client.server.show', ['id' => $server->id]) }}">{{ $server->id }}</a></td>
                                    <td>{{ $plan_model->where('id', $server->plan_id)->value('name') }}</td>
                                    <td>{{ $server->server_name }}</td>
                                    <td><span id="server_status_{{ $server->identifier }}"><span class="badge bg-warning">Loading</span></span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Support Tickets</h3>
                    <div class="card-tools">
                        <a href="{{ route('client.ticket.index') }}" class="btn btn-default btn-sm">View All Support Tickets <i class="fas fa-arrow-circle-right"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket_model->where('client_id', auth()->user()->id)->get() as $ticket)
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
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Unpaid Invoices</h3>
                    <div class="card-tools">
                        <a href="{{ route('client.invoice.index') }}" class="btn btn-default btn-sm">View All Invoices <i class="fas fa-arrow-circle-right"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice_model->where('client_id', auth()->user()->id)->where('paid', false)->get() as $invoice)
                                <tr>
                                    <td><a href="{{ route('client.invoice.index', ['id' => $invoice->id]) }}">{{ $invoice->id }}</a></td>
                                    <td>
                                        @if ($invoice->server_id)
                                            Server #{{ $invoice->server_id }}
                                        @elseif ($invoice->credit)
                                            {!! price($invoice->credit) !!} Credit
                                        @endif
                                    </td>
                                    <td>{!! price($invoice->total) !!}</td>
                                    <td>{{ $invoice->due_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Credit Transactions</h3>
                    <div class="card-tools">
                        <a href="{{ route('client.credit.show') }}" class="btn btn-default btn-sm">More details <i class="fas fa-arrow-circle-right"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Details</th>
                                <th>Change</th>
                                <th>Balance</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($credit_model->where('client_id', auth()->user()->id)->latest()->get() as $credit)
                                <tr>
                                    <td>{{ $credit->id }}</td>
                                    <td>{{ $credit->details }}</td>
                                    <td>
                                        @if ($credit->change < 0)
                                            -{!! session('currency')->symbol !!}{{ number_format(abs($credit->change) * session('currency')->rate, 2) }} 
                                        @else
                                            +{!! session('currency')->symbol !!}{{ number_format($credit->change * session('currency')->rate, 2) }} 
                                        @endif{{ session('currency')->name }}
                                    </td>
                                    <td>{!! session('currency')->symbol !!}{{ number_format($credit->balance * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                    <td>{{ $credit->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
