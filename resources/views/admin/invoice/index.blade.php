@extends('layouts.admin')

@inject('invoice_model', 'App\Models\Invoice')
@inject('client_model', 'App\Models\Client')
@inject('server_model', 'App\Models\Server')
@inject('tax_model', 'App\Models\Tax')

@section('title', 'Invoices')

@section('content')
    <div class="row justify-content-center">
        <div class="card col-12">
            <div class="card-header">
                <h3 class="card-title">Unpaid Invoices</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table id="unpaid-invoice-table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice_model->where('paid', false)->get() as $invoice)
                            <tr>
                                <td><a href="{{ route('admin.invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->id }}</a></td>
                                <td><a href="{{ route('admin.client.show', ['id' => $invoice->client_id]) }}" target="_blank">{{ $client_model->find($invoice->client_id)->email }}</a></td>
                                <td>
                                    @if ($invoice->server_id)
                                        Server #{{ $invoice->server_id }}
                                    @elseif ($invoice->credit)
                                        {!! price($invoice->credit) !!} Credit
                                    @endif
                                </td>
                                <td>{!! price($invoice->total) !!}</td>
                                <td>{{ $invoice->created_at }}</td>
                                <td>{{ $invoice->due_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card col-12">
            <div class="card-header">
                <h3 class="card-title">Paid Invoices</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table id="paid-invoice-table" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Invoice Date</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice_model->where('paid', true)->get() as $invoice)
                            <tr>
                                <td><a href="{{ route('admin.invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->id }}</a></td>
                                <td><a href="{{ route('admin.client.show', ['id' => $invoice->client_id]) }}" target="_blank">{{ $client_model->find($invoice->client_id)->email }}</a></td>
                                <td>
                                    @if ($invoice->server_id)
                                        Server #{{ $invoice->server_id }}
                                    @elseif ($invoice->credit)
                                        {!! price($invoice->credit) !!} Credit
                                    @endif
                                </td>
                                <td>{!! price($invoice->total) !!}</td>
                                <td>{{ $invoice->created_at }}</td>
                                <td>{{ $invoice->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Invoice Date</th>
                            <th>Paid Date</th>
                        </tr>
                    </tfoot>
                </table>
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
            $("#unpaid-invoice-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
            $("#paid-invoice-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
