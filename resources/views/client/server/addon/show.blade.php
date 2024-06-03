@extends('layouts.client')

@inject('plan_model', 'App\Models\Plan')
@inject('addon_model', 'App\Models\Addon')

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title m-0">Your Current Add-ons</h5>
                </div>
                <div class="card-body">
                    @foreach (json_decode($server->addon) as $addon)
                        @php
                            $addon_details = $addon_model->find($addon)->first();
                        @endphp
                        <h6 class="card-title">{{ $addon_details->name }}</h6><h6 class="card-title float-right">{!! session('currency_symbol') !!}{{ $addon_details->price }} {{ $server->billing_cycle }}</h6>
                        <p class="card-text">
                            <a href="{{ route('client.server.addon.remove', ['id' => $server->id, 'addon_id' => $addon_details->id]) }}" class="card-link text-danger">Remove Add-on</a>
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-7">
            @foreach ($addon_model->all() as $addon)
                @unless (in_array($addon->id, json_decode($server->addon)))
                    @if (!is_null(json_decode($addon->plans)))
                        @if (in_array($server->plan_id, json_decode($addon->plans)))
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-md-8">
                                        <h6 class="card-title">{{ $addon->name }}</h6><h6 class="card-title float-right">{!! session('currency_symbol') !!}{{ $addon->price }} {{ $server->billing_cycle }} ({!! session('currency_symbol') !!}{{ $addon->setup_fee }} setup fee)</h6>
                                    </div>
                                    <div class="col-md-4"><a href="{{ route('client.server.addon.add', ['id' => $server->id, 'addon_id' => $addon->id]) }}" class="btn btn-primary float-right">Order Add-on <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif (!is_null(json_decode($addon->categories)))
                        @if (in_array($plan_model->find($server->id)->value('category_id'), json_decode($addon->categories)))
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-md-8">
                                        <h6 class="card-title">
                                            {{ $addon->name }}<br>
                                            {!! session('currency_symbol') !!}{{ $addon->price }} {{ $server->billing_cycle }} ({!! session('currency_symbol') !!}{{ $addon->setup_fee }} setup fee)
                                        </h6>
                                    </div>
                                    <div class="col-md-4"><a href="{{ route('client.server.addon.add', ['id' => $server->id, 'addon_id' => $addon->id]) }}" class="btn btn-primary float-right">Order Add-on <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endunless
            @endforeach
        </div>
    </div>
@endsection