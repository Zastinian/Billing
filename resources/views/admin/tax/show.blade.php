@php $header_route = "admin.tax.index"; @endphp

@extends('layouts.admin')

@section('title', $tax->country)
@section('header', 'Taxes')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.tax.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="countryInput">Country Name</label>
                            <input type="text" name="country" value="{{ $tax->country }}" class="form-control" id="countryInput" placeholder="Country Name" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="percentInput">Tax Percentage (Optional)</label>
                            <input type="text" name="percent" value="{{ $tax->percent }}" class="form-control" id="percentInput" placeholder="Don't include a % symbol">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="amountInput">Tax Amount (Optional)</label>
                            <input type="text" name="amount" value="{{ $tax->amount }}" class="form-control" id="amountInput" placeholder="Tax Amount">
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.tax.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer col-lg-12 row justify-content-center">
                    <button type="submit" form="updateForm" class="btn btn-success btn-sm col-lg-2 col-3">Save</button>
                    <button type="submit" form="deleteForm" class="btn btn-danger btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
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
                waitRedirect('{{ route('admin.tax.index') }}')
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
