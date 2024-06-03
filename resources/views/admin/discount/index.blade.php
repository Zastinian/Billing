@extends('layouts.admin')

@inject('discount_model', 'App\Models\Discount')

@section('title', 'Discounts')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Discounts</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.discount.create') }}" class="btn btn-success btn-sm float-right">Create Discount <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="discounts-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Percent Off</th>
                                <th>Global?</th>
                                <th>Valid?</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($discount_model->all() as $discount)
                                <tr>
                                    <td><a href="{{ route('admin.discount.show', ['id' => $discount->id]) }}">{{ $discount->id }}</a></td>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ $discount->percent_off }}%</td>
                                    <td>
                                        @if ($discount->is_global)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>
                                        @if ($discount_model->verifyDiscount($discount))
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>{{ $discount->end_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Percent Off</th>
                                <th>Global?</th>
                                <th>Valid?</th>
                                <th>Expiry Date</th>
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
            $("#discounts-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
