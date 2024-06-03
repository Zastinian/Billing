@php $header_route = "admin.discount.index"; @endphp

@extends('layouts.admin')

@section('title', $discount->name)
@section('header', 'Discounts')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.discount.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf
                    <div class="card-body row">
                        <div class="form-group col-lg-6">
                            <label for="nameInput">Discount Name</label>
                            <input type="text" name="name" value="{{ $discount->name }}" class="form-control" id="nameInput"
                                placeholder="Discount Name" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="percentOffInput">Percent Off (One-time)</label>
                            <input type="text" name="percent_off" value="{{ $discount->percent_off }}" min="1" max="100" step="1"
                                class="form-control" id="percentOffInput" placeholder="Percent Off" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="isGlobalInput">Global? (can be used in the whole store?)</label>
                            <select class="form-control" name="is_global">
                                <option value="1" @if ($discount->is_global) selected @endif>Yes</option>
                                <option value="0" @unless ($discount->is_global) selected @endunless>No</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="endDateInput">End Date (optional)</label>
                            <input type="date" name="end_date" value="{{ str_replace(' 00:00:00', '', $discount->end_date) }}"
                                class="form-control" id="endDateInput">
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.discount.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
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
                waitRedirect('{{ route('admin.discount.index') }}')
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
