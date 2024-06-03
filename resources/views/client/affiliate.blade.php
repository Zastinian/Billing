@extends('layouts.client')

@inject('affiliate_model', 'App\Models\AffiliateEarning')

@section('title', 'Affiliate Program')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ auth()->user()->clicks }}</h3>
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
                    <h3>{{ auth()->user()->sign_ups }}</h3>
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
                    <h3>{{ auth()->user()->purchases }}</h3>
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
                    <h3>{!! session('currency')->symbol !!}{{ number_format(auth()->user()->commissions * session('currency')->rate, 2) }} {{ session('currency')->name }}</h3>
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
                    <h5 class="card-title">Your Referral Link:</h5>
                    <a href="{{ config('app.url') }}/a/{{ auth()->user()->id }}" class="float-right" target="_blank">{{ config('app.url') }}/a/{{ auth()->user()->id }}</a>
                </div>
            </div>
        </div>
        <div class="card col-12">
            <div class="card-header">
                <h3 class="card-title">Your Earnings</h3>
                <div class="card-tools">
                    <a href="{{ route('client.credit.show') }}" class="btn btn-default btn-sm float-right">View Credit Balance <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:5%">ID</th>
                            <th style="width:36%">Product</th>
                            <th style="width:12%">Commission</th>
                            <th style="width:12%">Conversion</th>
                            <th style="width:10%">Status</th>
                            <th style="width:25%">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($affiliate_model->where('client_id', auth()->user()->id) as $affiliate)
                            <tr>
                                <td>{{ $affiliate->id }}</a></td>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection