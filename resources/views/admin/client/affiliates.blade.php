@php $header_route = 'admin.client.index'; @endphp

@extends('layouts.admin')

@inject('client_model', 'App\Models\Client')
@inject('affiliate_model', 'App\Models\AffiliateEarning')

@section('title', $client->email)
@section('header', 'Clients')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <ul class="nav ml-auto p-2">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.client.show', ['id' => $id]) }}">Settings</a></li>
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.client.affiliates', ['id' => $id]) }}">Affiliates</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.client.credit', ['id' => $id]) }}">Credit</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $client->clicks }}</h3>
                    <p>Clicks</p>
                </div>
                <div class="icon">
                    <i class="far fa-hand-point-up"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $client->sign_ups }}</h3>
                    <p>Sign-ups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $client->purchases }}</h3>
                    <p>Purchases</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{!! session('currency')->symbol !!}{{ number_format($client->commissions * session('currency')->rate, 2) }} {{ session('currency')->name }}</h3>
                    <p>Commissions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="card col-12">
            <div class="card-body row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <h5 class="card-title">Client's Referral Link:</h5>
                    <a href="{{ config('app.url') }}/a/{{ $client->id }}" class="float-right" target="_blank">{{ config('app.url') }}/a/{{ $client->id }}</a>
                </div>
            </div>
        </div>
        <div class="card col-12">
            <div class="card-header">
                <h3 class="card-title">Affiliate Earnings</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Referer</th>
                            <th>Buyer</th>
                            <th>Product</th>
                            <th>Commission</th>
                            <th>Conversion</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($affiliate_model->where('client_id', $client->id)->get() as $affiliate)
                            <tr>
                                <td>{{ $affiliate->id }}</a></td>
                                <td>{{ $client_model->find($affiliate->client_id)->email }}</td>
                                <td>{{ $client_model->find($affiliate->buyer_id)->email }}</td>
                                <td>{{ $affiliate->product }}</td>
                                <td>{!! session('currency')->symbol !!}{{ number_format($affiliate->commission * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                <td>{{ $affiliate->conversion }}%</td>
                                <td>
                                    @switch($affiliate->status)
                                        @case(0)
                                            <span class="badge bg-success">Accepted</span>
                                            @break
                                        @case(1)
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case(2)
                                            <span class="badge bg-danger">Rejected</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $affiliate->created_at }}</td>
                                <td>
                                    @if ($affiliate->status === 1)
                                        <form action="{{ route('api.admin.affiliate.accept', ['id' => $$affiliate->id]) }}" method="POST" data-callback="actionForm">
                                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('api.admin.affiliate.reject', ['id' => $$affiliate->id]) }}" method="POST" data-callback="actionForm">
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<script>
    function actionForm(data) {
        if (data.success) {
            toastr.success(data.success)
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
</script>
