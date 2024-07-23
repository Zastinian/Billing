@extends('layouts.client')

@inject('credit_model', 'App\Models\Credit')
@inject('extension_model', 'App\Models\Extension')
@inject('extension_manager', 'Extensions\ExtensionManager')

@section('title', 'Account Credit')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{!! session('currency')->symbol !!}{{ number_format(auth()->user()->credit * session('currency')->rate, 2) }} {{ session('currency')->name }}</h3>
                    <p>Account Credit</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add Fund</h3>
                </div>
                <form action="{{ route('api.client.credit.add') }}" method="POST" data-callback="addForm">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="creditInput">Credit Amount</label>
                            <input type="text" name="credit" class="form-control" id="creditInput" placeholder="Credit Amount" required>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">Payment Method</label>
                            <select name="gateway" class="form-control">
                                @foreach ($extension_manager::$gateways as $gateway)
                                    @if ($extension_model->where(['extension' => $gateway::$display_name, 'key' => 'enabled'])->value('value') === '1')
                                    <option value="{{ $gateway::$display_name }}">{{ $gateway::$display_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Purchase Credit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Credit Transactions</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width:10%">ID</th>
                                <th style="width:30%">Details</th>
                                <th style="width:15%">Change</th>
                                <th style="width:15%">Balance</th>
                                <th style="width:30%">Date</th>
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
                                        @endif
                                        {{ session('currency')->name }}
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

@section('client_scripts')
    <script>
        function addForm(data) {
            if (data.info) {
                toastr.info(data.info)
                waitRedirect(data.url)
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
