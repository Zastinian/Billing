@extends('layouts.admin')

@inject('addon_model', 'App\Models\Addon')

@section('title', 'Plan Add-ons')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add-ons</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.addon.create') }}" class="btn btn-success btn-sm float-right">Create Add-on <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="addons-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Resource</th>
                                <th>Extra Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addon_model->all() as $addon)
                                <tr>
                                    <td>{{ $addon->order }}</td>
                                    <td><a href="{{ route('admin.addon.show', ['id' => $addon->id]) }}">{{ $addon->id }}</a></td>
                                    <td>{{ $addon->name }}</td>
                                    <td>
                                        @switch($addon->resource)
                                            @case('ram')
                                                RAM
                                                @break
                                            @case('cpu')
                                                CPU
                                                @break
                                            @case('disk')
                                                Disk
                                                @break
                                            @case('database')
                                                Database
                                                @break
                                            @case('backup')
                                                Backup
                                                @break
                                            @case('extra_port')
                                                Extra Port
                                                @break
                                            @case('dedicated_ip')
                                                Dedicated IP
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $addon->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Resource</th>
                                <th>Extra Amount</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); </script>

    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#addons-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
