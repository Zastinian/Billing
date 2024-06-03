@extends('layouts.admin')

@inject('plan_model', 'App\Models\Plan')
@inject('category_model', 'App\Models\Category')

@section('title', 'Server Plans')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Server Plans</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.plan.create') }}" class="btn btn-success btn-sm float-right">Create Server Plan <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="plans-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Plan Name</th>
                                <th>Category</th>
                                <th>RAM</th>
                                <th>CPU</th>
                                <th>Disk</th>
                                <th>DBs</th>
                                <th>Backups</th>
                                <th>Ports</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plan_model->all() as $plan)
                                <tr>
                                    <td>{{ $plan->order }}</td>
                                    <td><a href="{{ route('admin.plan.show', ['id' => $plan->id]) }}">{{ $plan->id }}</a></td>
                                    <td>{{ $plan->name }}</td>
                                    <td>{{ $category_model->find($plan->category_id)->name }}</td>
                                    <td>{{ $plan->ram }} MB</td>
                                    <td>{{ $plan->cpu }}%</td>
                                    <td>{{ $plan->disk }} MB</td>
                                    <td>{{ $plan->databases }}</td>
                                    <td>{{ $plan->backups }}</td>
                                    <td>+{{ $plan->extra_ports }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Plan Name</th>
                                <th>Category</th>
                                <th>RAM</th>
                                <th>CPU</th>
                                <th>Disk</th>
                                <th>DBs</th>
                                <th>Backups</th>
                                <th>Ports</th>
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
            $("#plans-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
