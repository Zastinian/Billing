@php $header_route = "admin.coupon.index"; @endphp

@extends('layouts.admin')

@inject('used_coupon_model', 'App\Models\UsedCoupon')
@inject('client_model', 'App\Models\Client')

@section('title', $coupon->code)
@section('header', 'Coupons')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.coupon.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="codeInput">Coupon Code</label>
                            <input type="text" name="code" value="{{ $coupon->code }}" class="form-control" id="codeInput" placeholder="Coupon Code" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="percentOffInput">Percent Off</label>
                            <input type="text" name="percent_off" value="{{ $coupon->percent_off }}" min="1" max="100" step="1" class="form-control" id="percentOffInput" placeholder="Percent Off" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="onetimeInput">One-time (first cycle only)?</label>
                            <select class="form-control" name="one_time">
                                <option value="1" @if ($coupon->one_time) selected @endif>Yes</option>
                                <option value="0" @unless ($coupon->one_time) selected @endunless>No</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="globalLimitInput">Global Limit (max. uses) (Optional)</label>
                            <input type="text" name="global_limit" value="{{ $coupon->global_limit }}" class="form-control" id="globalLimitInput" placeholder="0 = Cannot be used">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="perClientInput">Per Client Limit (max. uses per client) (Optional)</label>
                            <input type="text" name="per_client_limit" value="{{ $coupon->per_client_limit }}" class="form-control" id="perClientInput" placeholder="0 = Cannot be used">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="isGlobalInput">Global? (can be used in the whole store?)</label>
                            <select class="form-control" name="is_global">
                                <option value="1" @if ($coupon->is_global) selected @endif>Yes</option>
                                <option value="0" @unless ($coupon->is_global) selected @endunless>No</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="endDateInput">End Date (Optional)</label>
                            <input type="date" name="end_date" value="{{ str_replace(' 00:00:00', '', $coupon->end_date) }}" class="form-control" id="endDateInput">
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.coupon.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer col-lg-12 row justify-content-center">
                    <button type="submit" form="updateForm" class="btn btn-success btn-sm col-lg-2 col-3">Save</button>
                    <button type="submit" form="deleteForm" class="btn btn-danger btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Uses</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="used-coupons-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Server ID</th>
                                <th>Client</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($used_coupon_model->where('coupon_id', $id)->get() as $used_coupon)
                                <tr>
                                    <td><a href="{{ route('admin.server.show', ['id' => $coupon->server_id]) }}" target="_blank">{{ $coupon->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $coupon->client_id]) }}" target="_blank">{{ $client_model->find($coupon->client_id)->email }}</a></td>
                                    <td>{{ $coupon->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Server ID</th>
                                <th>Client</th>
                                <th>Date</th>
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
            $("#used-coupons-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
    
    <script>
        function updateForm(data) {
            if (data.success) {
                toastr.success(data.success)
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
                toastr.success(data.success)
                waitRedirect('{{ route('admin.coupon.index') }}')
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
