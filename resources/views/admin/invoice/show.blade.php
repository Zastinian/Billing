@php $header_route = 'admin.invoice.index'; @endphp

@extends('layouts.admin')

@inject('client_model', 'App\Models\Client')
@inject('tax_model', 'App\Models\Tax')

@section('title', 'Invoice Details')
@section('header', 'Invoices')
@section('subheader', "Invoice #{$id}")

@section('content')
    <div class="row" id="invoice_content">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="{{ config('app.logo_file_path') }}" height="50px" alt="{{ config('app.company_name') }} Logo"> {{ config('app.company_name') }}
                            <span class="float-right">
                                Status:
                                @if ($invoice->paid)
                                    Paid
                                @else
                                    Unpaid
                                @endif
                            </span>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        From
                        <address>
                            <strong>{{ config('app.company_name') }}</strong>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        To
                        <address>
                            <strong><a href="{{ route('admin.client.show', ['id' => $invoice->client_id]) }}" target="_blank">{{ $client_model->find($invoice->client_id)->email }}</a></strong>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>Invoice #{{ $invoice->id }}</b><br>
                        <b>Invoice Date:</b> {{ $invoice->created_at }}<br>
                        <b>Due Date:</b> {{ $invoice->due_date }}<br>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Products</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @if ($invoice->server_id)
                                            <a href="{{ route('admin.server.show', ['id' => $invoice->server_id]) }}">Server #{{ $invoice->server_id }}</a>
                                        @elseif ($invoice->credit)
                                            {!! price($invoice->credit) !!} Credit
                                        @endif
                                    </td>
                                    <td>{!! price($invoice->total) !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-5 offset-lg-4">
                        <p class="lead">Payment Method</p>
    
                        <div class="table-responsive">
                            <table class="table">
                                <tr><th>{{ $invoice->payment_method }}</th></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-3 col-5 offset-1">
                        <p class="lead">Amount Due</p>
    
                        <div class="table-responsive">
                            <table class="table">
                                <tr><th>{!! price($invoice->total) !!}</th></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
