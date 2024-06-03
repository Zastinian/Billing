@php $header_route = 'admin.servers.active'; @endphp

@extends('layouts.admin')

@inject('plan_model', 'App\Models\Plan')
@inject('plan_cycle_model', 'App\Models\PlanCycle')
@inject('addon_model', 'App\Models\Addon')
@inject('addon_cycle_model', 'App\Models\AddonCycle')
@inject('server_addon_model', 'App\Models\ServerAddon')

@section('title', 'Server Info')
@section('header', 'Servers')
@section('subheader', "Server #{$id}")

@php
    $plan = $plan_model->find($server->plan_id)->first();
    $plan_cycle = $plan_cycle_model->find($server->plan_cycle)->first();
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Server Information</h5>
                </div>
                <div class="card-body text-nowrap row">
                    <p class="card-text col-5">
                        <b>Panel ID</b><br>
                        <b>Server Identifier</b><br>
                        <b>Plan Name</b><br>
                    </p>
                    <p class="card-text col-7">
                        <a href="{{ config('app.panel_url') }}/admin/servers/view/{{ $server->server_id }}" target="_blank">{{ $server->server_id }}</a><br>
                        <a href="{{ config('app.panel_url') }}/server/{{ $server->identifier }}" target="_blank">{{ $server->identifier }}</a><br>
                        <a href="{{ route('admin.plan.show', ['id' => $plan->id]) }}" target="_blank">{{ $plan->name }}</a><br>
                    </p>
                    @if ($server->status === 2)
                        <form action="{{ route('api.admin.server.unsuspend', ['id' => $server->id]) }}" method="POST" data-callback="suspendForm">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm col-12">Unsuspend Server <i class="fas fa-arrow-circle-right"></i></button>
                        </form>
                    @else
                        <form action="{{ route('api.admin.server.suspend', ['id' => $server->id]) }}" method="POST" data-callback="suspendForm">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm col-12">Suspend Server <i class="fas fa-arrow-circle-right"></i></button>
                        </form>
                    @endif
                    @if ($server->status !== 1 && $server->status !== 3)
                        <form action="{{ route('api.admin.server.delete', ['id' => $server->id]) }}" method="POST" data-callback="deleteForm">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm col-12 offset-2">Delete/Cancel Server <i class="fas fa-arrow-circle-right"></i></button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Plan Information</h5>
                </div>
                <div class="card-body text-nowrap row">
                    <p class="card-text col-6">
                        <b>Renew Price</b><br>
                        <b>RAM</b><br>
                        <b>CPU</b><br>
                        <b>Disk</b><br>
                        <b>Databases</b><br>
                        <b>Backups</b><br>
                        <b>Extra Ports</b>
                    </p>
                    <p class="card-text col-6">
                        {!! price($plan_cycle->renew_price) !!}<br>
                        {{ $plan->ram }} MB<br>
                        {{ $plan->cpu }}%<br>
                        {{ $plan->disk }} MB<br>
                        {{ $plan->databases }}<br>
                        {{ $plan->backups }}<br>
                        {{ $plan->extra_ports }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Billing Overview</h5>
                </div>
                <div class="card-body text-nowrap row">
                    <p class="card-text col-7">
                        <b>Recurring Amount</b><br>
                        <b>Billing Cycle</b><br>
                        <b>Server Creation Date</b><br>
                        <b>Next Due Date</b><br>
                        <b>Payment Method</b>
                    </p>
                    <p class="card-text col-5">
                        {!! price($plan_cycle->renew_price) !!}<br>
                        {{ $plan_cycle->type_name($plan_cycle->cycle_length, $plan_cycle->cycle_type) }}<br>
                        {{ $server->created_at }}<br>
                        {{ $server->due_date }}<br>
                        {{ $server->payment_method }}
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add-ons added to this server</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Renew Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_addon_model->where('server_id', $id)->get() as $server_addon)
                                @php
                                    $addon = $addon_model->find($server_addon->addon_id);
                                    $addon_cycle = $addon_cycle_model->find($server_addon->cycle_id);
                                @endphp
                                <tr>
                                    <td><a href="{{ route('admin.addon.show', ['id' => $addon->id]) }}" target="_blank"></a>{{ $addon->id }}</td>
                                    <td>{{ $addon->name }}</td>
                                    <td>{{ $server_addon->value }}</td>
                                    <td>{!! price($addon_cycle->renew_price) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function suspendForm(data) {
            if (data.success) {
                toastr.info(data.success)
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
        
        function deleteForm(data) {
            if (data.success) {
                toastr.info(data.success)
                waitRedirect('{{ route('admin.servers.active') }}')
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
