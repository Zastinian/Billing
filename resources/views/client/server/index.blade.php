@extends('layouts.client')

@inject('server_model', 'App\Models\Server')
@inject('plan_model', 'App\Models\Plan')

@section('title', 'My Servers')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Servers</h3>
                    <div class="card-tools">
                        <a href="{{ config('app.panel_url') }}" class="btn btn-default btn-sm" target="_blank">View in Panel <i class="fas fa-arrow-circle-right"></i></a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Server Name</th>
                                <th>RAM (MB)</th>
                                <th>CPU (%)</th>
                                <th>Disk (MB)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_model->where(['client_id' => auth()->user()->id, 'status' => 0])->get() as $server)
                                <tr>
                                    <td><a href="{{ route('client.server.show', ['id' => $server->id]) }}">{{ $server->id }}</a></td>
                                    <td>{{ $plan_model->find($server->plan_id)->name }}</td>
                                    <td>{{ $server->server_name }}</td>
                                    <td><span id="memory_usage_{{ $server->identifier }}">{{ $plan_model->find($server->plan_id)->ram }}</span></td>
                                    <td><span id="cpu_usage_{{ $server->identifier }}">{{ $plan_model->find($server->plan_id)->cpu }}</span></td>
                                    <td><span id="disk_usage_{{ $server->identifier }}">{{ $plan_model->find($server->plan_id)->disk }}</span></td>
                                    <td><span id="server_status_{{ $server->identifier }}"><span class="badge bg-warning">Active</span></span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Servers</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Server Name</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_model->where(['client_id' => auth()->user()->id, 'status' => 1])->get() as $server)
                                <tr>
                                    <td>{{ $server->id }}</td>
                                    <td>{{ $plan_model->find($server->plan_id)->name }}</td>
                                    <td>{{ $server->server_name }}</td>
                                    <td>{{ $server->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Canceled Servers</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Server Name</th>
                                <th>Cancellation Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_model->where(['client_id' => auth()->user()->id, 'status' => 3])->get() as $server)
                                <tr>
                                    <td>{{ $server->id }}</td>
                                    <td>{{ $plan_model->find($server->plan_id)->name }}</td>
                                    <td>{{ $server->server_name }}</td>
                                    <td>{{ $server->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Suspended Servers</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Server Name</th>
                                <th>Suspension Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($server_model->where(['client_id' => auth()->user()->id, 'status' => 2])->get() as $server)
                                <tr>
                                    <td>{{ $server->id }}</td>
                                    <td>{{ $plan_model->find($server->plan_id)->name }}</td>
                                    <td>{{ $server->server_name }}</td>
                                    <td>{{ $server->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
