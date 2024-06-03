@php $header_route = "admin.tax.index"; @endphp

@extends('layouts.admin')

@section('title', 'Create Tax')
@section('header', 'Taxes')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.tax.create') }}" method="POST" data-callback="createForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="countryInput">Country Name</label>
                            <input type="text" name="country" class="form-control" id="countryInput" placeholder="Country Name" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="percentInput">Tax Percentage (Optional)</label>
                            <input type="text" name="percent" class="form-control" id="percentInput" placeholder="Don't include a % symbol">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="amountInput">Tax Amount (Optional)</label>
                            <input type="text" name="amount" class="form-control" id="amountInput" placeholder="Tax Amount">
                        </div>
                    </div>
                    <div class="card-footer col-lg-12 row justify-content-center">
                        <a href="{{ route('admin.tax.index') }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
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
