@extends('layouts.admin')

@inject('tax_model', 'App\Models\Tax')

@section('title', 'Taxes')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Taxes</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-success btn-sm float-right">Create Tax <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="taxes-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Country Name</th>
                                <th>Tax Percentage</th>
                                <th>Tax Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tax_model->all() as $tax)
                                <tr>
                                    <td><a href="{{ route('admin.tax.show', ['id' => $tax->id]) }}">{{ $tax->id }}</a></td>
                                    <td>{{ $tax->country }}</td>
                                    <td>{{ $tax->percent }}</td>
                                    <td>{{ $tax->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Country Name</th>
                                <th>Tax Percentage</th>
                                <th>Tax Amount</th>
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
            $("#taxes-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
