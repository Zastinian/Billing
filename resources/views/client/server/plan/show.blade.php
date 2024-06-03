@extends('layouts.client')

@inject('plan_model', 'App\Models\Plan')

@php
    $server_plan = $plan_model->find($server->plan_id);
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title m-0">Recently Purchased Plan</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">
                        Plan: {{ $server_plan->name }}<br>
                        Server Name: {{ $server->server_name }}
                    </h6>
                    <p class="card-text">
                        <ul class="list-unstyled">
                            <li>RAM <span class="float-right">{{ $server_plan->ram }} MB</span></li>
                            <li>CPU <span class="float-right">{{ $server_plan->cpu }} %</span></li>
                            <li>Disk <span class="float-right">{{ $server_plan->disk }} MB</span></li>
                            <li>Databases <span class="float-right">{{ $server_plan->databases }}</span></li>
                            <li>Backups <span class="float-right">{{ $server_plan->backups }}</span></li>
                            <li>Extra Ports <span class="float-right">{{ $server_plan->allocations - 0 }}</span></li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 row">
            @foreach ($plan_model->where('category_id', $plan_model->find($server->plan_id)->value('category_id'))->orderBy('order', 'desc')->get() as $plan)
                @if ($plan->id !== $server->plan_id)
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title m-0">{{ $plan->name }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">
                                    Plan: {{ $server_plan->name }}<br>
                                    Server Name: {{ $server->server_name }}
                                </h6>
                                <p class="card-text">
                                    <ul class="list-unstyled">
                                        <li>RAM <span class="float-right">{{ $plan->ram }} MB</span></li>
                                        <li>CPU <span class="float-right">{{ $plan->cpu }}%</span></li>
                                        <li>Disk <span class="float-right">{{ $plan->disk }} MB</span></li>
                                        <li>Databases <span class="float-right">{{ $plan->databases }}</span></li>
                                        <li>Backups <span class="float-right">{{ $plan->backups }}</span></li>
                                        <li>Extra Ports <span class="float-right">{{ $plan->allocations - 0 }}</span></li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection