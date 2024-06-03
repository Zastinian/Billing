@extends('layouts.admin')

@inject('coupon_model', 'App\Models\Coupon')
@inject('used_coupon_model', 'App\Models\UsedCoupon')

@section('title', 'Coupons')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Coupons</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.coupon.create') }}" class="btn btn-success btn-sm float-right">Create Coupon <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="coupons-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Percent Off</th>
                                <th>One-time?</th>
                                <th>Global?</th>
                                <th>Valid?</th>
                                <th>Total Uses</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupon_model->all() as $coupon)
                                <tr>
                                    <td><a href="{{ route('admin.coupon.show', ['id' => $coupon->id]) }}">{{ $coupon->id }}</a></td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->percent_off }}%</td>
                                    <td>
                                        @if ($coupon->one_time)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon->is_global)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon_model->verifyCoupon($coupon))
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>{{ $used_coupon_model->where('coupon_id', $coupon->id)->count() }}</td>
                                    <td>{{ $coupon->end_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Percent Off</th>
                                <th>One-time?</th>
                                <th>Global?</th>
                                <th>Valid?</th>
                                <th>Total Uses</th>
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
            $("#coupons-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
