@extends('layouts.admin')

@inject('category_model', 'App\Models\Category')

@section('title', 'Server Categories')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Server Categories</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.category.create') }}" class="btn btn-success btn-sm float-right">Create Category <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="categories-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Global Limit</th>
                                <th>Per Client Limit</th>
                                <th>Per Client Trial Limit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category_model->all() as $category)
                                <tr>
                                    <td>{{ $category->order }}</td>
                                    <td><a href="{{ route('admin.category.show', ['id' => $category->id]) }}">{{ $category->id }}</a></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->global_limit }}</td>
                                    <td>{{ $category->per_client_limit }}</td>
                                    <td>{{ $category->per_client_trial_limit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Global Limit</th>
                                <th>Per Client Limit</th>
                                <th>Per Client Trial Limit</th>
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
            $("#categories-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
