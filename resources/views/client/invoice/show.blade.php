@extends('layouts.client')

@inject('tax_model', 'App\Models\Tax')

@php
use App\Models\Server;
use App\Models\Plan;
@endphp

@section('content')
    @php
        $server_finded = Server::find($invoice->server_id);
        if($server_finded == NULL){
            $plan_finded = NULL;
        } else {
            $plan_finded = Plan::find($server_finded->plan_id);
        }
    @endphp
    @php
        $invoice_data = $invoice->payment_link;
    @endphp
    <div class="row" id="invoice_content">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="{{ config('app.logo_file_path') }}" height="50px" alt="Logo"> {{ config('app.company_name') }}
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
                            <strong>{{ auth()->user()->email }}</strong>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        @if ($plan_finded !== NULL)
                            <b>Product:</b> {{ $plan_finded->name }}
                        @else
                            <b>Product:</b> Credits
                        @endif
                        <br>
                        <b>Invoice Date:</b> {{ $invoice->created_at }}<br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:85%">Product</th>
                                    <th style="width:15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td>
                                            @if ($plan_finded !== NULL)
                                                {{ $plan_finded->name }}
                                            @else
                                                Credits
                                            @endif
                                        </td>
                                        <td>{!! price($invoice->total) !!}</td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-5">
                        <p class="lead">Payment Methods</p>
    
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width:35%">Primary</th>
                                    <td>{{ $invoice->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Backup</th>
                                    <td>Account Credit</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-5 offset-1">
                        <p class="lead">Amount Due</p>
    
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Total Due</th>
                                    <td>{!! price($invoice->total) !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row no-print">
                    <div class="col-12">
                        @if ($invoice->paid)
                        @else
                            <button onclick="pay_invoice('{{ $invoice_data }}')" class="btn btn-success float-right">
                                <i class="far fa-credit-card"></i> Pay Now
                            </button>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function pay_invoice(link)
        {
            window.location.href = link
        }
    </script>
@endsection