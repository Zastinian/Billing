@php $header_route = "admin.coupon.index"; @endphp

@extends('layouts.admin')

@section('title', 'Create Coupon')
@section('header', 'Coupons')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.coupon.create') }}" method="POST" data-callback="createForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="codeInput">Coupon Code</label>
                            <input type="text" name="code" class="form-control" id="codeInput" placeholder="Coupon Code" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="percentOffInput">Percent Off</label>
                            <input type="text" name="percent_off" min="1" max="100" step="1" class="form-control" id="percentOffInput" placeholder="Percent Off" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="onetimeInput">One-time (first cycle only)?</label>
                            <select class="form-control" name="one_time">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="globalLimitInput">Global Limit (max. uses) (Optional)</label>
                            <input type="text" name="global_limit" class="form-control" id="globalLimitInput" placeholder="0 = Cannot be used">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="perClientInput">Per Client Limit (max. uses per client) (Optional)</label>
                            <input type="text" name="per_client_limit" class="form-control" id="perClientInput" placeholder="0 = Cannot be used">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="isGlobalInput">Global? (can be used in the whole store?)</label>
                            <select class="form-control" name="is_global">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="endDateInput">End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control" id="endDateInput">
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('admin.discount.index') }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function createForm(data) {
            if (data.success) {
                toastr.success(data.success)
                resetForms()
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
