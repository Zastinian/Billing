@inject('tax_model', 'App\Models\Tax')

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} - {{ config('app.company_name') }}</title>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="/dist/css/adminlte.min.css">
    </head>

    <body>
        <div class="wrapper">
            <section class="invoice">
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
                                    <b>Invoice #{{ $invoice->id }}</b><br>
                                    <b>Invoice Date:</b> {{ $invoice->created_at }}<br>
                                    <b>Due Date:</b> {{ number_format($invoice->total_due * session('currency')->rate, 2) }}<br>
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
                                            @php
                                                $i = 0;
                                            @endphp
                                            @foreach ($invoice->products as $product)
                                                @php
                                                    $i++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $product }}</td>
                                                    <td>{!! session('currency')->symbol !!}{{ number_format($invoice->prices[$i] * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                                </tr>
                                            @endforeach
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
                                            @php
                                                $subtotal = 0.00;
                                                foreach ($invoice->prices as $price) {
                                                    $subtotal += $price;
                                                }
                                            @endphp
                                            <tr>
                                                <th style="width:60%">Subtotal</th>
                                                <td>{!! session('currency')->symbol !!}{{ number_format($subtotal * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tax ({{ $tax_model->find($invoice->tax_id)->percent }}%)</th>
                                                <td>+{!! session('currency')->symbol !!}{{ number_format($subtotal * session('currency')->rate * $tax_model->find($invoice->tax_id)->percent, 2) }} {{ session('currency')->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Due</th>
                                                <td>{!! session('currency')->symbol !!}{{ number_format($subtotal * ($tax_model->find($invoice->tax_id)->percent / 100) * session('currency')->rate) }} {{ session('currency')->name }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>