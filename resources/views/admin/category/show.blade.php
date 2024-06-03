@php $header_route = "admin.category.index"; @endphp

@extends('layouts.admin')

@section('title', $category->name)
@section('header', 'Server Categories')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.category.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="nameInput">Category Name</label>
                            <input type="text" name="name" value="{{ $category->name }}" class="form-control" id="nameInput" placeholder="Category Name" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="descriptionInput">Description (Optional)</label>
                            <textarea name="description" class="form-control" id="descriptionInput">{{ $category->description }}</textarea>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="{{ $category->order }}" value="1000" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.category.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer row justify-content-center">
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
                waitRedirect('{{ route('admin.category.index') }}')
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
