@php $header_route = "admin.currency.index"; @endphp

@extends('layouts.admin')

@section('title', $currency->name)
@section('header', 'Currencies')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.currency.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-3">
                            <label for="nameInput">3-Letter Currency Name</label>
                            <input type="text" name="name" value="{{ $currency->name }}" class="form-control" id="nameInput" placeholder="e.g. USD" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="symbolInput">Symbol (HTML Code)</label>
                            <input type="text" name="symbol" value="{{ $currency->symbol }}" class="form-control" id="symbolInput" placeholder="e.g. &#38;#36;" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="rateInput">Conversion Rate</label>
                            <input type="number" name="rate" value="{{ $currency->rate }}" class="form-control" id="rateInput" placeholder="Conversion Rate" @if ($currency->default) disabled @else required @endif>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="precisionInput">Precision</label>
                            <input type="number" name="precision" value="{{ $currency->precision }}" class="form-control" id="precisionInput" placeholder="Decimal Places" required>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="alert alert-info">
                                If 1 'default currency' = 1.01 'this currency', enter 1.01 as the conversion rate.
                            </div>
                        </div>
                        <hr>
                        <div class="form-group col-lg-3">
                            <label for="defaultInput">Default Currency</label>
                            <select class="form-control" name="default">
                                <option value="1" @if ($currency->default) selected @endif>Yes</option>
                                <option value="0" @unless ($currency->default) selected @endunless>No</option>
                            </select>
                        </div>
                        @unless ($currency->default)
                            <div class="form-group col-lg-9">
                                <div class="alert alert-danger">
                                    <b>WARNING!</b> You should avoid changing the default currency when your store has existing plans, add-ons, or invoices.
                                </div>
                            </div>
                        @endunless
                    </div>
                </form>
                <form action="{{ route('api.admin.currency.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
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
                waitRedirect('{{ route('admin.currency.index') }}')
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
