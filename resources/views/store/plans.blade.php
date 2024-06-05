@extends('layouts.store')
<?php

use App\Models\Plan;
use App\Models\Server;
use App\Models\Category;

?>

@inject('plan_model', 'App\Models\Plan')
@inject('plan_cycle_model', 'App\Models\PlanCycle')
@inject('category_model', 'App\Models\Category')
@inject('discount_model', 'App\Models\Discount')

@section('title', 'Server Plans')

@section('content')
    <div class="row">
        @isset($category2)
            <div class="col-lg-12">
                <div class="callout callout-info">
                    <h5>You are viewing the server plans of {{ $category2->name }}.</h5>
                </div>
            </div>
        @endisset
        @foreach ($plans as $plan)
            @php
                $plan_cycle = $plan_cycle_model->where('plan_id', $plan->id)->first();
                $plan_percent_off = 1;
            @endphp
            @foreach ($discount_model->getValidDiscounts() as $discount)
                @if ($plan->discount === $discount->id || $discount->is_global)
                    @php
                        $plan_percent_off = 1 - ($discount->percent_off / 100);
                        break;
                    @endphp
                @endif
            @endforeach
            @switch($plan_cycle->cycle_type)
                @case(0)
                    @php $cycle_type = 'One-time'; @endphp
                    @break
                @case(1)
                    @php $cycle_type = 'Hourly'; @endphp
                    @break
                @case(2)
                    @php $cycle_type = 'Daily'; @endphp
                    @break
                @case(3)
                    @php $cycle_type = 'Monthly'; @endphp
                    @break
                @case(4)
                    @php $cycle_type = 'Yearly'; @endphp
                    @break
            @endswitch
            @switch($plan_cycle->trial_type)
                @case(1)
                    @php $trial_type = 'Hour'; @endphp
                    @break
                @case(2)
                    @php $trial_type = 'Day'; @endphp
                    @break
                @case(3)
                    @php $trial_type = 'Month'; @endphp
                    @break
                @case(4)
                    @php $trial_type = 'Year'; @endphp
                    @break
            @endswitch
            <div class="col-lg-3 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">{{ $plan->name }}</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{!! session('currency')->symbol !!}{{ number_format($plan_cycle->init_price * session('currency')->rate * $plan_percent_off, 2) }} {{ session('currency')->name }} @unless ($plan_cycle->cycle_length === 1) / {{ $plan_cycle->cycle_length }} @endunless {{ $cycle_type }}</h6>
                        @if ($plan_cycle->trial_length)
                            <br><h6 class="card-title">{{ $plan_cycle->trial_length }} {{ $trial_type }}@unless ($plan_cycle->trial_length === 1) s @endunless Free Trial</h6>
                        @endif
                        <p class="card-text">
                            @php
                                $servers = Server::where('plan_id', $plan->id)
                                    ->where(function ($query) {
                                        $query->where('status', 0)->orWhere('status', 1);
                                    })->count();
                                $global_all_limit = $plan->global_limit === 0 ? 0 : ($plan->global_limit ? ($plan->global_limit - $servers) : null);
                            @endphp
                            @if (!is_null($global_all_limit))
                                @if ($global_all_limit > 0)
                                    <small>Only {{ $global_all_limit }} available!</small><br>
                                @else
                                    <small>0 disponibles</small><br>
                                @endif
                            @endif
                            <small>{{ $plan->description }}</small>
                            <ul class="list-unstyled">
                                <li>RAM <span class="float-right">{{ $plan->ram }} MB</span></li>
                                <li>CPU <span class="float-right">{{ $plan->cpu }}%</span></li>
                                <li>Disk <span class="float-right">{{ $plan->disk }} MB</span></li>
                                <li>Databases <span class="float-right">{{ $plan->databases }}</span></li>
                                <li>Backups <span class="float-right">{{ $plan->backups }}</span></li>
                                <li>Extra Ports <span class="float-right">{{ $plan->extra_ports }}</span></li>
                            </ul>
                        </p>
                        @if (!is_null($global_all_limit))
                            @if ($global_all_limit > 0)
                                <a href="{{ route('order', ['id' => $plan->id]) }}" class="btn btn-primary col-12">Order <i class="fas fa-arrow-circle-right"></i></a>
                            @else
                                <a href="#" class="btn btn-primary col-12 disabled">Order <i class="fas fa-arrow-circle-right"></i></a>
                            @endif
                        @else
                            <a href="{{ route('order', ['id' => $plan->id]) }}" class="btn btn-primary col-12">Order <i class="fas fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
